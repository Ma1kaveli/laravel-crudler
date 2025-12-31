<?php

namespace Tests\Fakes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FakeModel extends Model
{
    use SoftDeletes;

    protected $table = 'fake_models';

    protected $guarded = [];

    public $timestamps = false;
}
