<?php
namespace Reflexions\Content\Models;

class FileGroup extends \Eloquent
{
    public $timestamps = false;

    public function content()
    {
        $this->belongsTo(Content::class, 'content_id');
    }

    public function files()
    {
        return $this
            ->belongsToMany(File::class, 'file_file_groups', 'file_group_id', 'file_id')
            ->withPivot('seq')
            ->orderBy('file_file_groups.seq')
            ->orderBy('files.id');
    }

    public function setSequence($id_sequence)
    {
        if (empty($id_sequence)) {
            $id_sequence = [];
        }
        $id_sequence = array_filter($id_sequence);
        $this->files()->detach();

        foreach($id_sequence as $i => $id) {
            $this->files()->attach($id, ['seq' => $i]);
        }
    }

    public function setLead($lead_id)
    {
        $this
            ->files()
            ->detach();

        if (!empty($lead_id)) {
            $this
                ->files()
                ->attach($lead_id, ['seq' => 0]);
        }
    }
}
