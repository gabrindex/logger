<?php

namespace gabrindex\logger\Models;

use Illuminate\Database\Eloquent\Model;

class LoggerActivity extends Model
{
    public $timestamps = false;

    protected $appends = ["reference_url"];

    protected $fillable = ["instance_type", "instance_method", "reference_id", "log", "created_by", "created_at", "event_id", "edition_id", "source_type", "mocked_by"];

}
