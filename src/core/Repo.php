<?php
namespace FTumiwan\DomainCore;

use DB;
/**
 *
 */
class Repo
{

  protected $entity;

  function __construct($entity) {
    $this->entity = $entity;
  }

  public function getSchema($ent) {
    $_t = strtolower(str_plural(snake_case($ent)));
    return DB::getSchemaBuilder()->getColumnListing($_t);
  }

  public function store($val) {
    $m = $this->entity;
    $hs = $m::create($val);
    return $hs->id;
  }
  public function update($val,$id) {
    $m = $this->entity;
    $m::where('id',$id)->update($val);
    return "ok";
  }
  public function delete($id) {
    $m = $this->entity;
    $m::destroy($id);
    return "ok";
  }
  public function deleteWhere($fill) {
    $m = $this->entity;
    $qr = $m::select();
    foreach($fill as $key=>$val) {
      $qr->where($key,$val);
    }
    if ($qr->delete()) {
      return "ok";
    }
  }
  public function search($fill) {
    if (isset($fill['limit'])) {
      $l = $fill['limit'];
    } else {
      $l = 25;
    }
    $m = $this->entity;
    $qr = $m::select();
    foreach($fill['filter'] as $key=>$val) {
      if (isset($val['lgopr'])) {
        if ($val['lgopr']!=';'){
          $qr->where($val['fl'],$val['opr'],$val['vl'],$val['lgopr']);  
        }
      } else {
        $qr->where($val['fl'],$val['opr'],$val['vl']);
      }      
    }
    return $qr->paginate($l);
  }
  public function searchContainer($fill) {
    if (isset($fill['pr']['limit'])) {
      $l = $fill['pr']['limit'];
    } else {
      $l = 25;
    }
    $m = $this->entity;
    $qr = $m::select();
    foreach($fill['pr']['filter'] as $key=>$val) {
      if (isset($val['lgopr'])) {
        if ($val['lgopr']!=';'){
          $qr->where($val['fl'],$val['opr'],$val['vl'],$val['lgopr']);  
        }
      } else {
        $qr->where($val['fl'],$val['opr'],$val['vl']);
      }
    }
    return $qr->with($fill['with'])->paginate($l);
  }
  public function find($id) {
    $m = $this->entity;
    return $m::find($id);
  }
  public function findWith($pr) {  
    $m = $this->entity;    
    $_m = $m::where('id','=',$pr['id']);
    return $_m->with($pr['with'])->get();
  }
  public function queryAnd($fill) {
    $m = $this->entity;
    $qr = $m::select();
    foreach($fill as $key=>$val) {
      $qr->where($key,'=',$val);
    }
    return $qr->paginate(25);
  }
  public function queryAndContainer($fill) { //using at business domain
    $m = $this->entity;
    $qr = $m::select();
    foreach($fill['pr'] as $key=>$val) {
      $qr->where($key,'=',$val);
    }
    return $qr->with($fill['with'])->paginate(25);
  }
  public function loadAll($pr) {
    if (isset($pr['limit'])) {
      $l = $pr['limit'];
    } else {
      $l = 25;
    }
    $m = $this->entity;    
    $_m = $m::select();
    if (isset($pr['with'])) {
      return $_m->with($pr['with'])->paginate($l);
      // if ($pr['withparent']==true) {    
      //   if (count((new $m())->parentEntity())>0) {
      //     $_with = [];          
      //     foreach((new $m())->parentEntity() as $vp) {       
      //       $_with[] = $vp;
      //     }
      //     return $_m->with($_with)->paginate($l);  
      //   } else {
      //     return $_m->paginate($l);
      //   }
      // } else {
      //   return $_m->paginate($l);
      // }
    } else {
      return $_m->paginate($l);
    }
  }

}


?>
