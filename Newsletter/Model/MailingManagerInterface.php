<?php

namespace Wowo\NewsletterBundle\Newsletter\Model;

interface MailingManagerInterface
{
    /**
     * Creates mailing object based on form sent by user (basically title and e-mail body
     */
    public function createMailingBasedOnForm($form, $contactCount);

    /**
     * Finds mailing with given id
     */
    public function findMailing($id);
}
