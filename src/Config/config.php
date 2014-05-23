<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Evil attributes.
    |--------------------------------------------------------------------------
    |
    | Define here which are evil attributes. These attributes will be removed
    | from input.
    |
    */
    'evil-attributes' => array('on\w*', 'style', 'xmlns', 'formaction', 'form', 'xlink:href'),
);