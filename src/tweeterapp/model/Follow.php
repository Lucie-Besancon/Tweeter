<?php

namespace tweeterapp\model;

class Follow extends \Illuminate\Database\Eloquent\Model{
    protected $table      = 'follow';     /* nom table */
    protected $primaryKey = 'id';         /* nom clé primaire */
    public $timestamps = false;

    public static function getNbFollow($idUser){
      return Follow::where('follower', '=', $idUser)->count();
    }

}
