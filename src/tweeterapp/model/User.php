<?php

namespace tweeterapp\model;

class User extends \Illuminate\Database\Eloquent\Model{
    protected $table      = 'user';     /* nom table */
    protected $primaryKey = 'id';       /* nom clé primaire */
    public    $timestamps = false;

    public function tweets(){
      return $this->hasMany('tweeterapp\model\Tweet', 'author', 'id')->get();
    }

    public static function check_exist($id){
      $user = User::where('id', '=', $id)->first();
      if($user)
        return true;
      else
        return false;
    }

    public function followers($limit, $offset){
      $followers = $this->hasMany('tweeterapp\model\Follow', 'followee', 'id')->limit($limit)->offset($offset)->get();
      $list = [];
      foreach ($followers as $f) {
        array_push($list, $f->belongsTo('tweeterapp\model\User', 'follower')->select('id', 'fullname', 'username')->first());
      }
      return $list;
    }

    public function following($limit, $offset){
      $following = $this->hasMany('tweeterapp\model\Follow', 'follower', 'id')->limit($limit)->offset($offset)->get();
      $list = [];
      foreach ($following as $f) {
        array_push($list, $f->belongsTo('tweeterapp\model\User', 'followee')->select('id', 'fullname', 'username')->first());
      }
      return $list;
    }
}
