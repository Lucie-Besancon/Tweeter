<?php

namespace tweeterapp\model;

class Tweet extends \Illuminate\Database\Eloquent\Model{
  protected $table      = 'tweet';    /* nom table */
  protected $primaryKey = 'id';       /* nom clé primaire */
  public    $timestamps = true;

  public function author() {
    return $this->belongsTo('tweeterapp\model\User', 'author')->first();
  }
}
