<?php
namespace App\Helpers;
use App\Models\RefApprovalDetail;

class HrdFunction
{
    public static function set_approval_cek($group, $dept)
    {
        $queryCheck = RefApprovalDetail::where('approval_group', $group)->where('approval_departemen', $dept)->get()->count();
        return $queryCheck;
    }

    public static function set_approval_get_first($group, $dept)
    {
        $getFirst = RefApprovalDetail::where('approval_group', $group)->where('approval_departemen', $dept)->orderBy('approval_level', 'asc')->first()->approval_by_employee;
        return $getFirst;
    }

    public static function set_approval_new($group, $dept)
    {
        $queryAll = RefApprovalDetail::where('approval_group', $group)->where('approval_departemen', $dept)->get();
        return $queryAll;
    }
}