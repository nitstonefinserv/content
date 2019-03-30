<?php
namespace Reflexions\Content\Models\Admin;

use Reflexions\Content\Admin\AdminOptions;
use Reflexions\Content\Admin\TableRowAction;
use Reflexions\Content\Admin\Action;
use Reflexions\Content\Admin\Form;
use Reflexions\Content\Models\SiteSettings;
use DB;
use Datatables;
use URL;
use Auth;

class SiteSettingsAdminOptions extends AdminOptions {

    public function label() {
        return 'Site Settings';
    }

    public function pageIcon() {
        return "fa-cog";
    }

    // ------------------------------------
    //   List page options
    // ------------------------------------
    public function query() {
        return DB::table('site_settings')
            ->select('id', 'key', 'data');
    }

    public function datatables() {
        $query = $this->query();
        $datatables = Datatables::of($query);

        return $datatables;
    }

    public function tableColumns() {
        return [
            $this->column('id', 'ID'),
            $this->column('key', 'Key'),
            $this->column('data', 'Attributes', [], '75px'),
            $this->ActionColumn('Actions')
        ];
    }

    public function listActions() {
        return [
            new Action('Export', 'fa-download', URL::route('admin-' . $this->name() . '-export')),
        ];
    }

    public function rowActions($row) {
        return [
            new TableRowAction('Edit', 'fa fa-edit', URL::route('admin-site-settings-edit', $row->id)),
        ];
    }

    // ------------------------------------
    //   Edit page options
    // ------------------------------------
    public function createModel() {
        $settings = new SiteSettings();
        $settings->save();
        return $settings;
    }

    public function find($id) {
        return SiteSettings::find($id);
    }

    public function editForm($form, $model) {
        $form
            ->text('key', 'Key', ['disabled' => true])
            ->hidden('key', $model->key);

        foreach ($model->data as $key => $value)
        {
            $form->text($key, $key);
        }
    }
}