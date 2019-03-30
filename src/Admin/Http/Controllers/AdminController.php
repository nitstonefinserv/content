<?php
namespace Reflexions\Content\Admin\Http\Controllers;

use Content;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Redirect;
use ReflectionClass;
use Reflexions\Content\Admin\AdminOptions;
use Reflexions\Content\Admin\AdminOptionsBase;
use Reflexions\Content\Admin\Flash;
use Reflexions\Content\Admin\Form;
use Reflexions\Content\Admin\TableActionColumn;
use Reflexions\Content\Models\FileGroup;
use Reflexions\Content\Models\Slug;
use Reflexions\Content\Traits\ContentTrait;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Validator;
use View;


class AdminController extends Controller {
    const PASSWORD_PLACEHOLDER = '---PASSWORD---';

    /** @var  string */
    public $name;

    /** @var  AdminOptions */
    public $config;

    /**
     * Obtain configured instance of ReportsController
     *
     * @param string $name
     * @param string $config Name of config class extending AdminOptionsBase
     * @return AdminController
     */
    public static function factory($name, $config) {
        $instance = new AdminController();
        $instance->name = $name;
        $instance->config = new $config($name);
        return $instance;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index() {
        $name = $this->name;
        $config = $this->config;
        $rows = $config->query()->paginate($config->pagesize());
        return View::make(static::listViewName(), compact('rows', 'name', 'config'));
    }

    /**
     * Return ajax for jQuery.dataTables plugin
     *
     * @return JSONResponse
     */
    public function datatables() {
        $name = $this->name;
        $config = $this->config;

        $datatables = $config->datatables();

        // set default for when there are no rows
        $column_positions = array_flip(array_map(function ($e) {
            return $e->field;
        }, $config->tableColumns()));

        // the "order" parameter to datatables is in relation to the
        // original query, not the modified result.  The table columns
        // are ordered in sync with the modified result.
        // this builds up a lookup table of positions in order to know the
        // right order to use when adding the action column.
        //
        // one day I'll subclass laravel-datacolumns
        $query = clone self::_accessProtected($datatables, 'query');
        if (self::_accessProtected($datatables, 'query_type') == 'builder') {
            $columns = array_keys((array)$query->first());
        } else {
            $columns = array_keys((array)$query->getQuery()->first());
        }
        if (!empty($columns)) {
            $extra_columns = self::_accessProtected($datatables, 'extraColumns');
            $edit_columns = self::_accessProtected($datatables, 'columnDef')['edit'];
            $excess_columns = self::_accessProtected($datatables, 'columnDef')['excess'];
            $column_positions = array_flip($columns);
            foreach ($excess_columns as $name) {
                unset($column_positions[$name]);
            }
            foreach ($extra_columns as $meta) {
                $column_positions[$meta['name']] = $meta['order'];
            }
            foreach ($edit_columns as $meta) {
                $column_positions[$meta['name']]++;
            }
            asort($column_positions);
        }
        // end get column positions from data tables

        $position = 0;
        foreach ($config->tableColumns() as $i => $column) {
            if (is_a($column, TableActionColumn::class)) {
                $datatables->addColumn(
                    $column->field,
                    function ($row) use ($config) {
                        return json_encode($config->rowActions($row));
                    },
                    $position
                );
            } else {
                // $position = $column_positions[$column->field];
                $position++;
            }
        }
        return $datatables->make();
    }

    /**
     * Export data into download
     *
     * @return StreamedResponse
     */
    public function export() {
        $name = $this->name;
        $config = $this->config;

        return new StreamedResponse(function () use ($config) {
            $datatables = $config->datatables();

            // Open output stream
            $handle = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv(
                $handle,
                array_map(
                    function ($column) {
                        return $column->label;
                    },
                    array_filter(
                        $config->tableColumns(),
                        function ($column) {
                            return $column->field != TableActionColumn::ACTIONS_FIELDNAME;
                        }
                    )
                )
            );

            // prepare export but skip pagination
            $totalRecords = $datatables->totalCount();
            if ($totalRecords) {
                $datatables->orderRecords(true);
                $datatables->filterRecords();
                $datatables->orderRecords(false);

                // Badges? We don't need no stinkin' badges!
                foreach (\Yajra\Datatables\Helper::transform($datatables->getProcessedData(false)) as $row) {
                    fputcsv($handle, $row);
                }
            }

            // Close the output stream
            fclose($handle);
        }, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="export.csv"',
            ]
        );
    }

    /**
     * Show an edit form!
     */
    public function edit($id) {
        $name = $this->name;
        $config = $this->config;
        $model = $this->config->find($id);
        $form = new Form($model);
        $this->config->editForm($form, $model);
        return View::make(static::editViewName(), compact('id', 'name', 'config', 'model', 'form'));
    }

    /**
     * Create a new Instance
     */
    public function create() {
        $name = $this->name;
        $config = $this->config;
        $model = $this->config->createModel();
        $form = new Form($model);
        $this->config->editForm($form, $model);
        return View::make(static::editViewName(), compact('id', 'name', 'config', 'model', 'form'));
    }

    /**
     * Process create
     */
    public function store() {
        $model = $this->config->createModel();
        $form = new Form($model);
        $this->config->editForm($form, $model);

        $input = Input::all();
        foreach ($form->getFieldMutators() as $field => $mutator) {
            $input[$field] = $mutator($input[$field]);
        }

        $validator = Validator::make($input, $form->getFieldRules());
        if ($validator->fails()) {
            $title = count($validator->errors()->all()) > 1 ? ' Errors' : ' Error';

            Flash::overlay(count($validator->errors()->all()) . $title, $validator->errors()->all(), 'error');

            return Redirect::route('admin-' . $this->config->name() . '-create')
                ->withErrors($validator->errors())
                ->withInput();

        } else {
            foreach ($form->getFieldAttributes() as $field) {
                $value = $input[$field];
                $model->$field = $value;
            }
            $model->save();

            foreach ($form->getSavedHandlers() as $attribute => $saved_handler) {
                $saved_handler($attribute, $model);
            }

            $label = $model->name ?: $model->title;

            Flash::success('Success!', str_singular($this->config->label()) . ' ' . $label . ' updated.');

            return Redirect::route('admin-' . $this->config->name() . '-edit', $model->id);
            // ->with('success', str_singular($this->config->label()).' saved');
        }
    }

    /**
     * Process update
     */
    public function update($id) {
        $model = $this->config->find($id);
        $form = new Form($model);
        $this->config->editForm($form, $model);

        $input = Input::all();
        foreach ($form->getFieldMutators() as $field => $mutator) {
            $current_value = isset($input[$field]) ? $input[$field] : null;
            $input[$field] = $mutator($current_value);
        }

        $validator = Validator::make($input, $form->getFieldRules());
        if ($validator->fails()) {
            $title = count($validator->errors()->all()) > 1 ? ' Errors' : ' Error';

            Flash::overlay(count($validator->errors()->all()) . $title, $validator->errors()->all(), 'error');

            return Redirect::route('admin-' . $this->name . '-edit', $id)
                ->withErrors($validator->errors())
                ->withInput();

        } else {
            foreach ($form->getFieldAttributes() as $field) {
                $value = array_key_exists($field, $input) ? $input[$field] : '';
                $model->$field = $value;
            }

            $model->save();

            foreach ($form->getSavedHandlers() as $attribute => $saved_handler) {
                $saved_handler($attribute, $model);
            }

            $label = $model->name ?: $model->title;

            Flash::success('Success!', str_singular($this->config->label()) . ' ' . $label . ' updated.');

            return Redirect::route('admin-' . $this->name . '-edit', $id);
            // ->with('success', str_singular($this->config->label()).' Saved');
        }
    }

    /**
     * Process delete
     * Any other models depending on this model will have to be deleted
     * using laravel's boot method inside the this model's class, tying into
     * the static deleting method
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id) {
        $model = $this->config->find($id);

        DB::beginTransaction();
        try {
            // Only if model uses the ContentTrait
            if (
                in_array(ContentTrait::class, class_uses($model))
                && $model->content
            ) {
                $content = $model->content;
                // setting lead_image_id to null to disable foreign key constraint on record
                $content->lead_image_id = null;
                $content->save();

                // delete files and file groups
                foreach ($content->files as $file) {
                    $file->fileGroups()->detach();
                    foreach ($file->thumbnails as $thumbnail) {
                        $thumbnail->delete($this->config->delete_files_from_storage);
                    }
                    $file->delete($this->config->delete_files_from_storage);
                }
                FileGroup::where('content_id', $content->id)->delete();

                // delete tags
                DB::table('tagged')->where('content_id', $content->id)->delete();

                // delete slugs
                Slug::where('content_id', $content->id)->delete();

                // delete content
                $content->delete();
            }

            // delete model
            $model->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json($e->getMessage());
        }

        return response()->json(true);
    }

    /**
     * View to use to display edit page
     * @return string
     */
    public static function editViewName() {
        return Content::package() . "::admin.edit";
    }

    /**
     * Name of view to use to display list page
     * @return string
     */
    public static function listViewName() {
        return Content::package() . "::admin.list";
    }

    // http://stackoverflow.com/questions/20334355/how-to-get-protected-property-of-object-in-php
    protected static function _accessProtected($obj, $prop) {
        $reflection = new ReflectionClass($obj);
        $property = $reflection->getProperty($prop);
        $property->setAccessible(true);
        return $property->getValue($obj);
    }
}
