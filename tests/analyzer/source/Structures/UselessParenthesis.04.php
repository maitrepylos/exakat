<?php
      function getArray() { return [1, 2, 3]; }

      $last = array_pop(getArray());

      $last = array_pop((getArray()));
      
      getArray([1,2,3] + ([3, 4] + [5 + 6]));
?>