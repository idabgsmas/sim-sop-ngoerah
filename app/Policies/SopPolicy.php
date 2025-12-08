<?php

namespace App\Policies;

use App\Models\Sop;
use App\Models\TbUser;
use Illuminate\Auth\Access\Response;

class SopPolicy
{
    /**
     * Menentukan siapa yang boleh melihat daftar SOP di menu sidebar.
     */
    public function viewAny(TbUser $user): bool
    {
        // Izinkan Verifikator, dan Pengusul untuk melihat menu
        // Sesuaikan string role ini dengan data di tabel tb_role Anda
        return in_array($user->role->nama_role, ['Pengusul','Verifikator', 'Viewer']);
        // return true;
    }

    /**
     * Menentukan siapa yang boleh melihat detail satu SOP.
     */
    public function view(TbUser $tbUser, Sop $sop): bool
    {
        return True;
    }

    /**
     * Siapa yang boleh membuat SOP baru?
     */
    public function create(TbUser $user): bool
    {
        // Hanya Pengusul (Unit Kerja) yang boleh input
        return $user->role->nama_role === 'Pengusul';
    }

    /**
     * Siapa yang boleh edit?
     */
    public function update(TbUser $user, Sop $sop): bool
    {
        // Admin boleh segalanya
        // if ($user->role->nama_role === 'Administrator') {
        //     return true;
        // }

        // Pengusul hanya boleh edit jika statusnya masih Draft (1) atau Revisi (3)
        // DAN SOP itu milik unit kerjanya
        if ($user->role->nama_role === 'Pengusul') {
            $isMyUnit = $user->unitKerja->contains('id_unit_kerja', $sop->id_unit_kerja);
            $isEditableStatus = in_array($sop->id_status, [1, 3]);
            
            return $isMyUnit && $isEditableStatus;
        }

        return false;
    }

    /**
     * Siapa yang boleh hapus?
     */
    public function delete(TbUser $user, Sop $sop): bool
    {
        // if ($user->role->nama_role === 'Administrator') {
        //     return true;
        // }
        
        // Pengusul hanya boleh hapus jika status Draft
        if ($user->role->nama_role === 'Pengusul') {
            return $sop->id_unit_kerja == $user->unitKerja->first()?->id_unit_kerja 
                   && $sop->id_status === 1;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(TbUser $tbUser, Sop $sop): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(TbUser $tbUser, Sop $sop): bool
    {
        return false;
    }
}
