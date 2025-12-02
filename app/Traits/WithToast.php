<?php

namespace App\Traits;

trait WithToast
{
    public function alertSuccess($message)
    {
        $this->dispatch('notify', ['type' => 'success', 'message' => $message]);
    }

    public function alertError($message)
    {
        $this->dispatch('notify', ['type' => 'error', 'message' => $message]);
    }

    /**
     * Kirim notifikasi info/warning ke frontend (Opsional).
     * * @param string $message Pesan yang akan ditampilkan
     */
    public function alertInfo($message)
    {
        $this->dispatch('notify', [
            'type' => 'info',
            'message' => $message
        ]);
    }
}
