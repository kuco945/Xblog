<?php
/**
 * Created by PhpStorm.
 * User: lufficc
 * Date: 2016/10/5
 * Time: 0:00
 */

namespace App\Observers;


use App\Contracts\XblogCache;
use App\Http\Controllers\Admin\FileableController;
use App\Http\Controllers\GeneratedController;
use App\Post;

class PostObserver
{
    protected $xblogCache;
    protected $fileableController;

    public function __construct(FileableController $fileableController)
    {
        $this->fileableController = $fileableController;
    }

    public function saved(Post $post)
    {
        $this->fileableController->syncPost($post);
        $this->getXblogCache()->clearCache();
    }

    /**
     * @return XblogCache
     */
    private function getXblogCache()
    {
        if ($this->xblogCache == null) {
            $this->xblogCache = app('XblogCache');
            $this->xblogCache->setTag(GeneratedController::tag);
        }
        return $this->xblogCache;
    }
}