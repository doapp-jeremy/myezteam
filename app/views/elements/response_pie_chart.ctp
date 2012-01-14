<?php // views/elements/responsePieChart.ctp : renders a pie chart for responses
//$responses : (optional -- if not set, $eventId or $event must be) : array of responses
?>

<?php
if (!isset($default))
{
  $default = 'no_response';
}
$labels = array();
$colors = array();
$values = array();
$responseTypes = getResponseTypes();
foreach ($responseTypes as $responseType)
{
  $responseType = array_pop($responseTypes);
  if (($responseType['ResponseType']['name'] != 'no_response') || ($default == 'no_response'))
  {
    $rsvpId = $responseType['ResponseType']['id'];
    $responseName = $responseType['ResponseType']['name'];
    array_push($colors, $responseType['ResponseType']['color']);
    $value = 0;
    if (isset($responses[$rsvpId]))
    {
      $value = count($responses[$rsvpId]);
    }
    array_push($values, $value);
    $label = Inflector::humanize($responseName) . ' (' . $value . ')';
    if ($default == $responseName)
    {
      $label .= ' - Default';
    }
    array_push($labels, $label);
  }
}

$url = 'http://chart.apis.google.com/chart?';
$url .= 'cht=p3';
if (isset($chartSize))
{
  $url .= '&chs=' . $chartSize;
}
else
{
  $url .= '&chs=600x240';
}
$url .= '&chd=t:' . implode(',', $values);
$url .= '&chco=' . implode(',', $colors);
//$url .= '&chl=' . implode('|', $labels);
echo $html->image($url);

?>