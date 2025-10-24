<?php

use businessGenerator\include\VariaTools;

include_once "include/VariaTools.php";
(new VariaTools())->doRemoteServiceCall("OrderRemoteService");
