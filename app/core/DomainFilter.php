<?php 
/**
 * Interface used for implementing domain filtering
 */
interface DomainFilter {
	public function isPresent($url);
}
?>
