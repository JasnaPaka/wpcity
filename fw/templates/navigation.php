<div class="tablenav-pages">
	<span class="displaying-num">Položek: <?php echo $controller->getCount(); ?></span>
	
<?php if ($controller->getShowNavigation()) { ?>		
	<span class="pagination-links">
	
		<a href="admin.php?page=object&amp;paged=1" class="first-page" title="Přejít na první stránku">«</a>
		<a href="admin.php?page=object&amp;paged=<?php echo $controller->getPagePrevious() ?>" class="prev-page" title="Přejít na předchozí stránku">‹</a>
		
		<?php echo $controller->getPageCurrent() ?> z <?php echo $controller->getPageLast() ?>		
		
		<a href="admin.php?page=object&amp;paged=<?php echo $controller->getPageNext() ?>" class="next-page" title="Přejít na následující stránku">›</a>
		<a href="admin.php?page=object&amp;paged=<?php echo $controller->getPageLast() ?>" class="last-page" title="Přejít na poslední stránku">»</a>

	</span>
<?php } ?>		
</div>

	

