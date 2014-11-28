<?php 
	// Řazení
	if (count($controller->getOrders()) > 0) {
		$currentOrder = $controller->getCurrentOrder();
		
		echo '<ul class="subsubsub"><strong>Řazení dle</strong>: ';
		
		$prvni = true;
		foreach ($controller->getOrders() as $order) {
			if ((strlen($currentOrder) == 0 && $prvni) || $order->url === $currentOrder) {
				echo '<li class="all">'.($prvni? "": "&nbsp;|&nbsp;").''.$order->nazev.'</li>';
			} else {
				echo '<li class="publish">'.($prvni? "": "&nbsp;|&nbsp;").'<a href="admin.php?page='.$controller->getStringId().'&amp;action=list&amp;order='.$order->url.'">'.$order->nazev.'</a></li>';
			}
			$prvni = false;
		}
		
		echo '</ul>';
	}
?>