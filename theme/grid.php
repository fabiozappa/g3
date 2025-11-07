<?php
header("Content-type: text/css");
$theme = "base";
if(!empty($_GET['theme'])) $theme = $_GET['theme'];
include("./".$theme."/themeconfig.php");
/**
Breakpoint:
•	col- → default mobile
•	col-sm- → ≥ 576px (tablet verticale)
•	col-md- → ≥ 768px (tablet orizzontale)
•	col-lg- → ≥ 992px (laptop)
•	col-xl- → ≥ 1200px (desktop grande)

*/
?>
#content .row {
  display: flex;
  flex-wrap: wrap;
  margin-left: -8px;
  margin-right: -8px;
}

#content [class*="col-"] {
  padding: 8px;
  box-sizing: border-box;
}

#content {
  --col-1: 8.3333%;
  --col-2: 16.6666%;
  --col-3: 25%;
  --col-4: 33.3333%;
  --col-5: 41.6666%;
  --col-6: 50%;
  --col-7: 58.3333%;
  --col-8: 66.6666%;
  --col-9: 75%;
  --col-10: 83.3333%;
  --col-11: 91.6666%;
  --col-12: 100%;
}

#content .col-1   { width: var(--col-1); }
#content .col-2   { width: var(--col-2); }
#content .col-3   { width: var(--col-3); }
#content .col-4   { width: var(--col-4); }
#content .col-5   { width: var(--col-5); }
#content .col-6   { width: var(--col-6); }
#content .col-7   { width: var(--col-7); }
#content .col-8   { width: var(--col-8); }
#content .col-9   { width: var(--col-9); }
#content .col-10  { width: var(--col-10); }
#content .col-11  { width: var(--col-11); }
#content .col-12  { width: var(--col-12); }

/* Responsive breakpoints */

@media (min-width: 576px) {
  #content .col-sm-1   { width: var(--col-1); }
  #content .col-sm-2   { width: var(--col-2); }
  #content .col-sm-3   { width: var(--col-3); }
  #content .col-sm-4   { width: var(--col-4); }
  #content .col-sm-5   { width: var(--col-5); }
  #content .col-sm-6   { width: var(--col-6); }
  #content .col-sm-7   { width: var(--col-7); }
  #content .col-sm-8   { width: var(--col-8); }
  #content .col-sm-9   { width: var(--col-9); }
  #content .col-sm-10  { width: var(--col-10); }
  #content .col-sm-11  { width: var(--col-11); }
  #content .col-sm-12  { width: var(--col-12); }
}

@media (min-width: 768px) {
  #content .col-md-1   { width: var(--col-1); }
  #content .col-md-2   { width: var(--col-2); }
  #content .col-md-3   { width: var(--col-3); }
  #content .col-md-4   { width: var(--col-4); }
  #content .col-md-5   { width: var(--col-5); }
  #content .col-md-6   { width: var(--col-6); }
  #content .col-md-7   { width: var(--col-7); }
  #content .col-md-8   { width: var(--col-8); }
  #content .col-md-9   { width: var(--col-9); }
  #content .col-md-10  { width: var(--col-10); }
  #content .col-md-11  { width: var(--col-11); }
  #content .col-md-12  { width: var(--col-12); }
}

@media (min-width: 992px) {
  #content .col-lg-1   { width: var(--col-1); }
  #content .col-lg-2   { width: var(--col-2); }
  #content .col-lg-3   { width: var(--col-3); }
  #content .col-lg-4   { width: var(--col-4); }
  #content .col-lg-5   { width: var(--col-5); }
  #content .col-lg-6   { width: var(--col-6); }
  #content .col-lg-7   { width: var(--col-7); }
  #content .col-lg-8   { width: var(--col-8); }
  #content .col-lg-9   { width: var(--col-9); }
  #content .col-lg-10  { width: var(--col-10); }
  #content .col-lg-11  { width: var(--col-11); }
  #content .col-lg-12  { width: var(--col-12); }
}

@media (min-width: 1200px) {
  #content .col-xl-1   { width: var(--col-1); }
  #content .col-xl-2   { width: var(--col-2); }
  #content .col-xl-3   { width: var(--col-3); }
  #content .col-xl-4   { width: var(--col-4); }
  #content .col-xl-5   { width: var(--col-5); }
  #content .col-xl-6   { width: var(--col-6); }
  #content .col-xl-7   { width: var(--col-7); }
  #content .col-xl-8   { width: var(--col-8); }
  #content .col-xl-9   { width: var(--col-9); }
  #content .col-xl-10  { width: var(--col-10); }
  #content .col-xl-11  { width: var(--col-11); }
  #content .col-xl-12  { width: var(--col-12); }
}