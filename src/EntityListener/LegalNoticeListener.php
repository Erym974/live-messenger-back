<?php

namespace App\EntityListener;

use App\Entity\LegalNotice;

class LegalNoticeListener
{

    public function prePersist(LegalNotice $legalNotice)
    {
        $legalNotice->setUpdatedAt(new \DateTimeImmutable());
    }

    public function preUpdate(LegalNotice $legalNotice) {
        $legalNotice->setUpdatedAt(new \DateTimeImmutable());
    }


}