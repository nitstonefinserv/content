<div class="row">
    <div class="col-xs-6 col-sm-2">
        <a href="{{ URL::route('admin-'.$config->name().'-index') }}" class="btn btn-danger">Cancel, Go back</a>
    </div>
    
    <div class="col-xs-6 col-sm-10">
        <div class="pull-right">
            <button class="btn btn-success" type="submit">Save</button>
        </div>
    </div>
</div>