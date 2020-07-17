<?php 
$postid=get_the_ID();
$bg_attr='';
	
	if((!empty($display_thumbnail) && $display_thumbnail=='yes') && !empty($thumbnail)){
		$featured_image=get_the_post_thumbnail_url($postid,$thumbnail);		
	}else{
		$featured_image=get_the_post_thumbnail_url($postid,'full');		
	}
		
	if((!empty($display_thumbnail) && $display_thumbnail=='yes') && !empty($thumbnail)){
		if ( !empty($featured_image) ) {
			$bg_attr='style="background:url('.$featured_image.') #f7f7f7;"';			
		}else{
			$bg_attr = theplus_loading_image_grid($postid,'background');			
		}
	}else{
		if ( !empty($featured_image) ) {
			$bg_attr=theplus_loading_bg_image($postid);			
		}else{
			$bg_attr = theplus_loading_image_grid($postid,'background');			
		}
	}
?>

<div class="bss-wrapper">
			<div class="post-content-image-bg" <?php echo $bg_attr; ?> >
				<div class="post-content-wrapper">					
						<?php if($display_post_category=='yes'){ ?>
							<?php include THEPLUS_INCLUDES_URL. 'dynamic-smart-showcase/blog-category-'.$post_category_style.'.php'; ?>
						<?php } ?>	
						<div class="bss-content">
								<?php include THEPLUS_INCLUDES_URL. 'dynamic-smart-showcase/post-meta-title.php'; ?>
								<div class="bss-meta-content"><a href="<?php echo esc_url(get_the_permalink()); ?>" class="bss-meta-content-link">
									<?php if(!empty($display_post_meta) && $display_post_meta=='yes'){ ?>
											<?php include THEPLUS_INCLUDES_URL. 'dynamic-smart-showcase/blog-post-meta-new-'.$post_meta_tag_style.'.php'; ?>
									<?php } ?></a>																	
								</div>						
					</div>
				</div>
			</div>
			<div class="post-content-remain-list">	
				<a href="<?php echo esc_url(get_the_permalink()); ?>" class="bss-remain-img">
					<?php include THEPLUS_INCLUDES_URL. 'dynamic-smart-showcase/format-image.php'; ?>
				</a>
				<div class="bss-content">				
						<div class="bss-meta-content"><a href="<?php echo esc_url(get_the_permalink()); ?>" class="bss-meta-content-link">
							<?php if(!empty($display_post_meta) && $display_post_meta=='yes'){ ?>
									<?php include THEPLUS_INCLUDES_URL. 'dynamic-smart-showcase/blog-post-meta-'.$post_meta_tag_style.'.php'; ?>
							<?php } ?></a>
						</div>
						<?php include THEPLUS_INCLUDES_URL. 'dynamic-smart-showcase/post-meta-title.php'; ?>																
				</div>
			</div>		
</div>