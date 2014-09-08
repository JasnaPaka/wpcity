<div class="tablenav-pages">
	<span class="displaying-num">Položek: <?php echo $controller->getCount(); ?></span>
	
<?php if ($controller->getShowNavigation()) { 
	$order = $controller->getCurrentOrder();
?>		
	<span class="pagination-links">
	
		<a href="admin.php?page=object&amp;paged=1" class="first-page" title="Přejít na první stránku">«</a>
		<a href="admin.php?page=object&amp;paged=<?php echo $controller->getPagePrevious() ?>&amp;order=<?php echo $order ?>" class="prev-page" title="Přejít na předchozí stránku">‹</a>
		
		<?php echo $controller->getPageCurrent() ?> z <?php echo $controller->getPageLast() ?>		
		
		<a href="admin.php?page=object&amp;paged=<?php echo $controller->getPageNext() ?>&amp;order=<?php echo $order ?>" class="next-page" title="Přejít na následující stránku">›</a>
		<a href="admin.php?page=object&amp;paged=<?php echo $controller->getPageLast() ?>&amp;order=<?php echo $order ?>" class="last-page" title="Přejít na poslední stránku">»</a>

	</span>
<?php } ?>		
</div>

	

