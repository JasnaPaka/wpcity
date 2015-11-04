<div class="tablenav-pages">
	<span class="displaying-num">Položek: <?php echo $controller->getCount(); ?></span>
	
<?php if ($controller->getShowNavigation()) { 
	$order = $controller->getCurrentOrder();
?>		
	<span class="pagination-links">
	
		<a href="admin.php?page=<?php echo $controller->getStringId() ?>&amp;paged=1<?php echo $controller->getAdminUrlParams() ?>" 
				class="first-page" title="Přejít na první stránku">«</a>
		<a href="admin.php?page=<?php echo $controller->getStringId() ?>&amp;paged=<?php echo $controller->getPagePrevious() ?><?php echo $controller->getAdminUrlParams() ?>&amp;order=<?php echo $order ?>" 
				class="prev-page" title="Přejít na předchozí stránku">‹</a>
		
		<?php echo $controller->getPageCurrent() ?> z <?php echo $controller->getPageLast() ?>		
		
		<a href="admin.php?page=<?php echo $controller->getStringId() ?>&amp;paged=<?php echo $controller->getPageNext() ?><?php echo $controller->getAdminUrlParams() ?>&amp;order=<?php echo $order ?>" class="next-page" title="Přejít na následující stránku">›</a>
		<a href="admin.php?page=<?php echo $controller->getStringId() ?>&amp;paged=<?php echo $controller->getPageLast() ?><?php echo $controller->getAdminUrlParams() ?>&amp;order=<?php echo $order ?>" class="last-page" title="Přejít na poslední stránku">»</a>

	</span>
<?php } ?>		
</div>

	

