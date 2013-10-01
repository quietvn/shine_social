<?php 

$mongo = MisfitMongo::getInstance()->collection;

$id = new MongoId('519c611c9f12e5522a000009');

$query = array( "uid" => $id);
//$cursor = $mongo->users->find( $query );
$cursor = $mongo->goals->find( $query );

// $goal = $mongo->goals->findOne();
// print_r($goal);die();

while ( $cursor->hasNext() )
{
	var_dump( $cursor->getNext() );
}
die();


$user = $mongo->users->findOne();
print_r($user);die();