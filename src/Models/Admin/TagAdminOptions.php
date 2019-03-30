<?php
namespace Reflexions\Content\Models\Admin;

use Auth;
use Reflexions\Content\Admin\AdminOptions;
use Datatables;
use URL;
use Reflexions\Content\Models\Tag;
use Reflexions\Content\Models\Status;
use Reflexions\Content\Admin\Form;

class TagAdminOptions extends AdminOptions {
    
    public function label(){
        return 'Tag';
    }

    public function pageIcon() {
        return "fa-tag";
    }

    // ------------------------------------
    //   List page options
    // ------------------------------------    
    public function query()
    {
        return Tag::withContent()
            ->select('tags.id', 'tags.term_group_name', 'tags.name', 'content.slug')
            ->where('content.publish_status', '!=', Status::STUB);
    }
    
    public function datatables()
    {
        $query = $this->query();
        $datatables = Datatables::of($query);
        return $datatables;
    }

    public function tableColumns()
    {
        return [
            $this->column('id', 'ID'),
            $this->column('term_group_name', 'Group'),
            $this->column('name', 'Name'),
            $this->column('slug', 'Slug', ['name' => 'content.slug']),
            $this->actionColumn('Actions'),
        ];
    }

    public function listActions() {
        return [
            $this->action('Export', 'fa-download', URL::route('admin-'.$this->name().'-export')),
            $this->action('Create', 'fa-plus', URL::route('admin-'.$this->name().'-create'))
        ];
    }

    public function rowActions($row)
    {
        return [
            $this->action('Edit', 'fa fa-edit', URL::route('admin-'.$this->name().'-edit', $row->id)),
        ];
    }

    public function order() {
        return [[1, "asc"]];
    }

    // ------------------------------------
    //   Edit page options
    // ------------------------------------
    public function createModel() {
        // $instance = Tag::withContent()
        //     ->where('content.publish_status', Status::STUB)
        //           ->where('content.user_id', Auth::user()->id)
        //     ->first();
        // if (empty($instance)) {
        //     $instance = new Tag();
        //     $instance->save();
        // }
        $instance = new Tag();
        $instance->publish_status = Status::DRAFT;
        return $instance;
    }

    public function find($id)
    {
        return Tag::find($id);
    }

    public function editForm(Form $form, Tag $model)
    {
        $form
            ->text('name', 'Name', [Form::OPTIONS_VALIDATION => 'required'])
            ->text('term_group_name', 'Group', [Form::OPTIONS_VALIDATION => 'required'])
            ->text('term_group_slug', 'Group Slug', [Form::OPTIONS_VALIDATION => 'required'])
            ->addField($model->getPublishableAdminFields([
                'use_lead_image' => false,
                'use_slug' => true,
            ]));
    }
}
