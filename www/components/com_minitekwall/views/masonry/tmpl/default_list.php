<?php
/**
* @title			Minitek Wall
* @copyright   		Copyright (C) 2011-2014 Minitek, All rights reserved.
* @license   		GNU General Public License version 3 or later.
* @author url   	http://www.minitek.gr/
* @developers   	Minitek.gr
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

if (!empty($this->wall) ||  $this->wall!== 0)
{ 
	$options = $this->getColumnsItemOptions();
	
	foreach ($this->wall as $key=>$item) 
	{
		// Cat Filters
		$catfilter = '';
		if (isset($item->itemCategoriesRaw))
		{
			foreach($item->itemCategoriesRaw as $category) 
			{
				if (is_array($category))
				{
					$catfilter .= ' cat-'.$this->utilities->cleanName($category['category_name']);
				}
				else
				{
					// For Easyblog categories
					$catfilter .= ' cat-'.$this->utilities->cleanName($category->getTitle());	
				}
			}
		}
		else if (isset($item->itemCategoryRaw)) 
		{
			$catfilter .= ' cat-'.$this->utilities->cleanName($item->itemCategoryRaw);
		}		
		
		// Tag Filters
		$tagfilter = '';
		if (isset($item->itemTags)) 
		{
			foreach($item->itemTags as $tag_name) 
			{
				$tagfilter .= ' tag-'.$this->utilities->cleanName($tag_name->title);
			}
		}
		else if (isset($item->itemAuthorsRaw)) 
		{
			$manufacturerModel = VmModel::getModel('Manufacturer');
			foreach($item->itemAuthorsRaw as $key=>$itemManufacturer) 
			{
				$manufacturer = $manufacturerModel->getManufacturer((int)$itemManufacturer);						
				$tagfilter .= ' tag-'.$this->utilities->cleanName($manufacturer->mf_name);
			}
		}
		
		// Location Filters
		$locationfilter = '';
		if (isset($item->itemLocationRaw)) 
		{
			$locationfilter .= ' location-'.$this->utilities->cleanName($item->itemLocationRaw);
		}		
		
		// Date Filters
		$datefilter = '';
		if (isset($item->itemDateRaw)) 
		{
			$datefilter .= ' date-'.JHTML::_('date', $item->itemDateRaw, 'Y-m');
		}	
		?>
	
		<div 
			class="mnwall-item 
			<?php echo $catfilter; ?>
			<?php echo $locationfilter; ?>
			<?php echo $tagfilter; ?>
			<?php echo $datefilter; ?>
			<?php echo $this->hoverEffectClass; ?>" 
			style="padding:<?php echo (int)$this->gutter; ?>px;"
			data-title="<?php echo strtolower($item->itemTitleRaw); ?>"
			<?php if (isset($item->itemCategoryRaw)) { ?>
				data-category="<?php echo strtolower($this->utilities->cleanName($item->itemCategoryRaw)); ?>"
			<?php } ?>
			<?php if (isset($item->itemCategoriesRaw)) { ?>
				data-category="<?php echo strtolower($item->itemCategories); ?>"
			<?php } ?>
			<?php if (isset($item->itemLocationRaw)) { ?>
				data-location="<?php echo strtolower($this->utilities->cleanName($item->itemLocationRaw)); ?>"
			<?php } ?>
			<?php if (isset($item->itemAuthorRaw)) { ?>
				data-author="<?php echo strtolower($item->itemAuthorRaw); ?>"
			<?php } ?>
			<?php if (isset($item->itemAuthorsRaw)) { ?>
				data-author="<?php echo strtolower($item->itemAuthors); ?>"
			<?php } ?>
			<?php if (isset($item->itemDateRaw)) { ?>
				data-date="<?php echo $item->itemDateRaw; ?>"
			<?php } ?>
		>
		
			<div 
				class="
				mnwall-item-outer-cont 
				<?php if ($this->detailBoxColumns && (isset($item->itemImage) && $item->itemImage && $this->mas_images))
				{
					echo $options['position_class'];
				} ?>
				"
				style="
				<?php if ($this->mas_border_radius) { ?>
					border-radius: <?php echo $this->mas_border_radius; ?>px;
				<?php } ?>
				<?php if ($this->mas_border) { ?>
					border: <?php echo $this->mas_border; ?>px solid <?php echo $this->mas_border_color; ?>;
				<?php } ?>
				<?php echo $this->animated_flip; ?>
				"
			>
	
				<div 
					class="mnwall-item-inner-cont"
					style="background-color: rgba(<?php echo $options['db_bg_class']; ?>,<?php echo $options['db_bg_opacity_class']; ?>);"
				>
					
					<?php if (isset($item->itemImage) && $item->itemImage && $this->mas_images) { ?>
					
						<div class="mnwall-cover <?php echo $this->hoverEffectClass; ?>">
							
							<div class="mnwall-img-div" style=" <?php echo $this->animated_flip; ?>">
								
								<div class="mnwall-item-img">
									
									<?php if (isset($item->itemLink)) { ?>		
										<a 
											href="<?php echo $item->itemLink; ?>" 
											class="mnwall-photo-link">
											<img src="<?php echo $item->itemImage; ?>" alt="" />
										</a>
									<?php } else { ?>
										<div 
											class="mnwall-photo-link">
											<img src="<?php echo $item->itemImage; ?>" alt="" />
										</div>
									<?php } ?>
						
								</div>
								
								<?php if ($this->hoverBox) { ?>
												
									<div 
										class="mnwall-hover-box" 
										style=" 
											<?php echo $this->animated; ?>
											background-color: rgba(<?php echo $this->hb_bg_class; ?>,<?php echo $this->hb_bg_opacity_class; ?>);
										"
									>
																				
										<div class="mnwall-hover-box-content <?php echo $this->hoverTextColor; ?>">
											
											<?php if ($this->hoverBoxDate && isset($item->itemDate)) { ?>
												<div class="mnwall-date">
													<?php echo $item->itemDate; ?>
												</div>
											<?php } ?>
											
											<?php if ($this->hoverBoxTitle) { ?>
												<h3 class="mnwall-title">
													<?php if (isset($item->itemLink)) { ?>
														<a href="<?php echo $item->itemLink; ?>">
															<?php echo $item->hover_itemTitle; ?>
														</a>
													<?php } else { ?>
														<span>
															<?php echo $item->hover_itemTitle; ?>
														</span>
													<?php } ?>	
												</h3>
											<?php } ?>
											
											<?php if ($this->hoverBoxCategory || $this->hoverBoxLocation || $this->hoverBoxType || $this->hoverBoxAuthor) { ?>
												<div class="mnwall-item-info">
												
													<?php if (((isset($item->itemCategoryRaw) && $item->itemCategoryRaw) || (isset($item->itemCategoriesRaw) && $item->itemCategoriesRaw)) && $this->hoverBoxCategory) { ?>
														<p class="mnwall-item-category">
															<span><?php echo JText::_('COM_MINITEKWALL_IN'); ?> </span>
															<?php echo $item->itemCategory; ?>
														</p>
													<?php } ?>
													
													<?php if (isset($item->itemLocationRaw) && $item->itemLocationRaw && $this->hoverBoxLocation) { ?>
														<p class="mnwall-item-category">
															<span><?php echo JText::_('COM_MINITEKWALL_IN'); ?> </span>
															<?php echo $item->itemLocation; ?>
														</p>
													<?php } ?>
													
													<?php if ($this->hoverBoxType) { ?>
														<p class="mnwall-item-category">
															<?php echo $item->itemType; ?>
														</p>
													<?php } ?>
																												
													<?php if (((isset($item->itemAuthorRaw) && $item->itemAuthorRaw) || (isset($item->itemAuthorsRaw) && $item->itemAuthorsRaw)) && $this->hoverBoxAuthor) { ?>
														<p class="mnwall-item-author">
															<span><?php echo JText::_('COM_MINITEKWALL_BY'); ?> </span>
															<?php echo $item->itemAuthor; ?>
														</p>
													<?php } ?>
																					
												</div>
											<?php } ?>
																							
											<?php if ($this->hoverBoxIntrotext) { ?>							
												<?php if (isset($item->hover_itemIntrotext) && $item->hover_itemIntrotext) { ?>
													<div class="mnwall-desc">
														<?php echo $item->hover_itemIntrotext; ?>
													</div>
												<?php } ?>							
											<?php } ?>
											
											<?php if ($this->hoverBoxPrice && isset($item->itemPrice)) { ?>
												<div class="mnwall-price">
													<?php echo $item->itemPrice; ?>
												</div>
											<?php }?>
											
											<?php if ($this->hoverBoxHits && isset($item->itemHits)) { ?>
												<div class="mnwall-hits">
													<p><?php echo $item->itemHits; ?>&nbsp;<?php echo JText::_('COM_MINITEKWALL_HITS'); ?></p>
												</div>
											<?php }?>
																	
											<?php if ($this->hoverBoxLinkButton || $this->hoverBoxFancyButton) { ?>
												<div class="mnwall-item-icons">
													<?php if ($this->hoverBoxLinkButton) { ?>
														<?php if (isset($item->itemLink)) { ?>
															<a href="<?php echo $item->itemLink; ?>" class="mnwall-item-link-icon">
																<i class="fa fa-link"></i>
															</a>
														<?php } ?>
													<?php } ?>
													
													<?php if ($this->hoverBoxFancyButton) { ?>
														<a href="<?php echo $item->itemImage; ?>" class="fancybox mnwall-item-fancy-icon" rel="group">
															<i class="fa fa-search"></i>
														</a>
													<?php } ?>
												</div>
											<?php } ?>
											
										</div>
										
									</div>
									
								<?php } ?>
								
							</div>
							
						</div>
					
					<?php } ?>	
					
					<?php if ($this->detailBoxAll) { ?>
					
						<div 
							class="
							mnwall-item-inner mnwall-detail-box
							<?php echo $options['db_color_class']; ?>
							<?php echo $options['db_class']; ?>
							<?php echo $options['title_class']; ?>
							<?php echo $options['introtext_class']; ?>
							<?php echo $options['date_class']; ?>
							<?php echo $options['category_class']; ?>
							<?php echo $options['location_class']; ?>
							<?php echo $options['type_class']; ?>
							<?php echo $options['author_class']; ?>
							<?php echo $options['price_class']; ?>
							<?php echo $options['hits_class']; ?>
							<?php echo $options['count_class']; ?>
							<?php echo $options['readmore_class']; ?>
							<?php if (!isset($item->itemImage) || !$item->itemImage || !$this->mas_images) 
							{
								echo 'mnw-no-image';
							} ?>
							"
						>
							
							<?php if ($this->detailBoxDateAll && isset($item->itemDate)) { ?>
								<div class="mnwall-date">
									<?php echo $item->itemDate; ?>
								</div>
							<?php } ?>
							
							<?php if ($this->detailBoxTitleAll) { ?>
								<h3 class="mnwall-title">
									<?php if (isset($item->itemLink)) { ?>
										<a href="<?php echo $item->itemLink; ?>">
											<?php echo $item->itemTitle; ?>
										</a>	
									<?php } else { ?>
										<span>
											<?php echo $item->itemTitle; ?>
										</span>
									<?php } ?>
								</h3>
							<?php } ?>
							
							<?php if (($this->detailBoxCategoryAll && ((isset($item->itemCategoryRaw) && $item->itemCategoryRaw) || (isset($item->itemCategoriesRaw) && $item->itemCategoriesRaw))) || ($this->detailBoxLocationAll && isset($item->itemLocationRaw)) || ($this->detailBoxAuthorAll && ((isset($item->itemAuthorRaw) && $item->itemAuthorRaw) || (isset($item->itemAuthorsRaw) && $item->itemAuthorsRaw))) || $this->detailBoxTypeAll) { ?>
								<div class="mnwall-item-info">
									
									<?php if ($this->detailBoxCategoryAll && ((isset($item->itemCategoryRaw) && $item->itemCategoryRaw) || (isset($item->itemCategoriesRaw) && $item->itemCategoriesRaw))) { ?>
										<p class="mnwall-item-category">
											<span><?php echo JText::_('COM_MINITEKWALL_IN'); ?> </span>
											<?php echo $item->itemCategory; ?>
										</p>
									<?php } ?>
									
									<?php if ($this->detailBoxLocationAll && isset($item->itemLocationRaw) && $item->itemLocationRaw) { ?>
										<p class="mnwall-item-location">
											<span><?php echo JText::_('COM_MINITEKWALL_IN'); ?> </span>
											<?php echo $item->itemLocation; ?>
										</p>
									<?php } ?>
									
									<?php if ($this->detailBoxTypeAll) { ?>
										<p class="mnwall-item-type">
											<?php echo $item->itemType; ?>
										</p>
									<?php } ?>
																		
									<?php if ($this->detailBoxAuthorAll && ((isset($item->itemAuthorRaw) && $item->itemAuthorRaw) || (isset($item->itemAuthorsRaw) && $item->itemAuthorsRaw))) { ?>
										<p class="mnwall-item-author">
											<span><?php echo JText::_('COM_MINITEKWALL_BY'); ?> </span>
											<?php echo $item->itemAuthor; ?>
										</p>
									<?php } ?>
																			
								</div>
							<?php } ?>
							
							<?php if ($this->detailBoxIntrotextAll && isset($item->itemIntrotext) && $item->itemIntrotext) { ?>
								<div class="mnwall-desc">
									<?php echo $item->itemIntrotext; ?>
								</div>
							<?php } ?>							
							
							<?php if ($this->detailBoxPriceAll && isset($item->itemPrice)) { ?>
								<div class="mnwall-price">
									<?php echo $item->itemPrice; ?>
								</div>
							<?php } ?>
							
							<?php if ($this->detailBoxHitsAll && isset($item->itemHits)) { ?>
								<div class="mnwall-hits">
									<p><?php echo $item->itemHits; ?>&nbsp;<?php echo JText::_('COM_MINITEKWALL_HITS'); ?></p>
								</div>
							<?php } ?>
							
							<?php if ($this->detailBoxCountAll && isset($item->itemCount)) { ?>
								<div class="mnwall-count">
									<p><?php echo $item->itemCount; ?></p>
								</div>
							<?php } ?>
							
							<?php if ($this->detailBoxReadmoreAll) { ?>
								<?php if (isset($item->itemLink)) { ?>
									<div class="mnwall-readmore">
										<a href="<?php echo $item->itemLink; ?>"><?php echo JText::_('COM_MINITEKWALL_READ_MORE'); ?></a>
									</div>
								<?php } ?>
							<?php } ?>
																												
						</div>
						
					<?php } ?>
					
					<?php if (! $this->mas_images || !isset($item->itemImage) || !$item->itemImage) { ?>
				
						<?php if ($this->hoverBox) { ?>
													
							<div 
								class="mnwall-hover-box" 
								style=" 
									<?php echo $this->animated; ?>
									background-color: rgba(<?php echo $this->hb_bg_class; ?>,<?php echo $this->hb_bg_opacity_class; ?>);
								"
							>
																
								<div class="mnwall-hover-box-content <?php echo $this->hoverTextColor; ?>">
									
									<?php if ($this->hoverBoxDate && isset($item->itemDate)) { ?>
										<div class="mnwall-date">
											<?php echo $item->itemDate; ?>
										</div>
									<?php } ?>
									
									<?php if ($this->hoverBoxTitle) { ?>
										<h3 class="mnwall-title">
											<?php if (isset($item->itemLink)) { ?>
												<a href="<?php echo $item->itemLink; ?>">
													<?php echo $item->hover_itemTitle; ?>
												</a>	
											<?php } else { ?>
												<span>
													<?php echo $item->hover_itemTitle; ?>
												</span>
											<?php } ?>
										</h3>
									<?php } ?>
									
									<?php if ($this->hoverBoxCategory || $this->hoverBoxLocation || $this->hoverBoxType || $this->hoverBoxAuthor) { ?>
										<div class="mnwall-item-info">
										
											<?php if (((isset($item->itemCategoryRaw) && $item->itemCategoryRaw) || (isset($item->itemCategoriesRaw) && $item->itemCategoriesRaw)) && $this->hoverBoxCategory) { ?>
												<p class="mnwall-item-category">
													<span><?php echo JText::_('COM_MINITEKWALL_IN'); ?> </span>
													<?php echo $item->itemCategory; ?>
												</p>
											<?php } ?>
											
											<?php if (isset($item->itemLocationRaw) && $item->itemLocationRaw && $this->hoverBoxLocation) { ?>
												<p class="mnwall-item-category">
													<span><?php echo JText::_('COM_MINITEKWALL_IN'); ?> </span>
													<?php echo $item->itemLocation; ?>
												</p>
											<?php } ?>
											
											<?php if ($this->hoverBoxType) { ?>
												<p class="mnwall-item-category">
													<?php echo $item->itemType; ?>
												</p>
											<?php } ?>
																										
											<?php if (((isset($item->itemAuthorRaw) && $item->itemAuthorRaw) || (isset($item->itemAuthorsRaw) && $item->itemAuthorsRaw)) && $this->hoverBoxAuthor) { ?>
												<p class="mnwall-item-author">
													<span><?php echo JText::_('COM_MINITEKWALL_BY'); ?> </span>
													<?php echo $item->itemAuthor; ?>
												</p>
											<?php } ?>
																			
										</div>
									<?php } ?>
																					
									<?php if ($this->hoverBoxIntrotext) { ?>							
										<?php if (isset($item->hover_itemIntrotext) && $item->hover_itemIntrotext) { ?>
											<div class="mnwall-desc">
												<?php echo $item->hover_itemIntrotext; ?>
											</div>
										<?php } ?>							
									<?php } ?>
									
									<?php if ($this->hoverBoxPrice && isset($item->itemPrice)) { ?>
										<div class="mnwall-price">
											<?php echo $item->itemPrice; ?>
										</div>
									<?php }?>
									
									<?php if ($this->hoverBoxHits && isset($item->itemHits)) { ?>
										<div class="mnwall-hits">
											<p><?php echo $item->itemHits; ?>&nbsp;<?php echo JText::_('COM_MINITEKWALL_HITS'); ?></p>
										</div>
									<?php }?>
															
									<?php if ($this->hoverBoxLinkButton || $this->hoverBoxFancyButton) { ?>
										<div class="mnwall-item-icons">
											<?php if ($this->hoverBoxLinkButton) { ?>
												<?php if (isset($item->itemLink)) { ?>
													<a href="<?php echo $item->itemLink; ?>" class="mnwall-item-link-icon">
														<i class="fa fa-link"></i>
													</a>
												<?php } ?>
											<?php } ?>
											
											<?php if ($this->hoverBoxFancyButton) { ?>
												<a href="<?php echo $item->itemImage; ?>" class="fancybox mnwall-item-fancy-icon" rel="group">
													<i class="fa fa-search"></i>
												</a>
											<?php } ?>
										</div>
									<?php } ?>
									
								</div>
								
							</div>
							
						<?php } ?>	
					
					<?php } ?>
										
				</div>
							
			</div>
			
		</div>
	
	<?php }
	
} else {
	echo '-'; // =0 // for javascript purposes - Do not remove
}
