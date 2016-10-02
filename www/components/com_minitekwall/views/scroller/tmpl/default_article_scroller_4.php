<?php
/**
* @title			Minitek Wall
* @copyright   		Copyright (C) 2011-2016 Minitek, All rights reserved.
* @license   		GNU General Public License version 3 or later.
* @author url   	http://www.minitek.gr/
* @developers   	Minitek.gr
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

if (!empty($this->wall) ||  $this->wall!== 0)
{ 	
	foreach ($this->wall as $key=>$item) 
	{ ?>
	
		<div 
			class="mnwall-scr-item 
			<?php echo $this->hoverEffectClass; ?>" 
			style="
			padding-left:<?php echo $this->gutter; ?>px;
			padding-right:<?php echo $this->gutter; ?>px;
			"
		>
		
			<div 
				class="mnwall-scr-item-outer-cont"
				style=" <?php echo $this->animated_flip; ?>"
			>
	
				<div 
					class="mnwall-scr-item-inner-cont"
					style="
					<?php if ($this->border_radius) { ?>
						border-radius: <?php echo $this->border_radius; ?>px;
					<?php } ?>
					<?php if ($this->border) { ?>
						border: <?php echo $this->border; ?>px solid <?php echo $this->border_color; ?>;
					<?php } ?>
					"
				>
					
					<div class="mnwall-scr-item-cover"> </div>
					
					<?php if (isset($item->itemImage) && $item->itemImage && $this->scr_images) { ?>
					
						<div 
							class="
							mnwall-scr-cover 
							<?php echo $this->hoverEffectClass; ?>
							<?php if (!$this->detailBox) {
								echo 'no_detail_box';
							} ?>
							"
							style="height: <?php echo $this->scr_image_height; ?>px;"
						>
							
							<div 
								class="mnwall-scr-img-div" 
								style=" <?php echo $this->animated_flip; ?>"
							>
								
								<div class="mnwall-scr-item-img">
									
									<?php if (isset($item->itemLink)) { ?>		
										<a 
											href="<?php echo $item->itemLink; ?>" 
											class="mnwall-scr-photo-link">
											<img src="<?php echo $item->itemImage; ?>" alt="" />
										</a>
									<?php } else { ?>
										<div 
											class="mnwall-scr-photo-link">
											<img src="<?php echo $item->itemImage; ?>" alt="" />
										</div>
									<?php } ?>
						
								</div>
								
								<?php if ($this->hoverBox && ($this->hoverBoxEffect !== '2' && $this->hoverBoxEffect !== '3')) { ?>
												
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
											<?php } ?>
										
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
														<?php } else { ?>
															<span>
																<i class="fa fa-link"></i>
															</span>
														<?php } ?>
													<?php } ?>
													
													<?php if ($this->hoverBoxFancyButton) { ?>
														<a href="<?php echo $item->itemImageRaw; ?>" class="fancybox mnwall-item-fancy-icon" rel="group">
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
					
					<?php if ($this->detailBox) { ?>
					
						<div 
							class="mnwall-scr-detail-box clearfix
							<?php echo $this->detailBoxTextColor; ?>
							"
							style="background-color: rgba(<?php echo $this->detailBoxBackground; ?>,<?php echo $this->detailBoxBackgroundOpacity; ?>);"
						>
							
							<?php if ($this->detailBoxDate && isset($item->itemDate)) { ?>
								<div class="mnwall-date">
									<?php echo $item->itemDate; ?>
								</div>
							<?php } ?>
							
							<?php if ($this->detailBoxTitle) { ?>
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
							
							<?php if (($this->detailBoxCategory && ((isset($item->itemCategoryRaw) && $item->itemCategoryRaw) || (isset($item->itemCategoriesRaw) && $item->itemCategoriesRaw))) || ($this->detailBoxLocation && isset($item->itemLocationRaw)) || ($this->detailBoxAuthor && ((isset($item->itemAuthorRaw) && $item->itemAuthorRaw) || (isset($item->itemAuthorsRaw) && $item->itemAuthorsRaw))) || $this->detailBoxType) { ?>
								<div class="mnwall-item-info">
									
									<?php if ($this->detailBoxCategory && ((isset($item->itemCategoryRaw) && $item->itemCategoryRaw) || (isset($item->itemCategoriesRaw) && $item->itemCategoriesRaw))) { ?>
										<p class="mnwall-item-category">
											<span><?php echo JText::_('COM_MINITEKWALL_IN'); ?> </span>
											<?php echo $item->itemCategory; ?>
										</p>
									<?php } ?>
									
									<?php if ($this->detailBoxLocation && isset($item->itemLocationRaw) && $item->itemLocationRaw) { ?>
										<p class="mnwall-item-location">
											<span><?php echo JText::_('COM_MINITEKWALL_IN'); ?> </span>
											<?php echo $item->itemLocation; ?>
										</p>
									<?php } ?>
									
									<?php if ($this->detailBoxType) { ?>
										<p class="mnwall-item-type">
											<?php echo $item->itemType; ?>
										</p>
									<?php } ?>
																		
									<?php if ($this->detailBoxAuthor && ((isset($item->itemAuthorRaw) && $item->itemAuthorRaw) || (isset($item->itemAuthorsRaw) && $item->itemAuthorsRaw))) { ?>
										<p class="mnwall-item-author">
											<span><?php echo JText::_('COM_MINITEKWALL_BY'); ?> </span>
											<?php echo $item->itemAuthor; ?>
										</p>
									<?php } ?>
																			
								</div>
							<?php } ?>
							
							<?php if ($this->detailBoxIntrotext && isset($item->itemIntrotext) && $item->itemIntrotext) { ?>
								<div class="mnwall-desc">
									<?php echo $item->itemIntrotext; ?>
								</div>
							<?php } ?>							
							
							<?php if ($this->detailBoxPrice && isset($item->itemPrice)) { ?>
								<div class="mnwall-price">
									<?php echo $item->itemPrice; ?>
								</div>
							<?php } ?>
							
							<?php if ($this->detailBoxHits && isset($item->itemHits)) { ?>
								<div class="mnwall-hits">
									<p><?php echo $item->itemHits; ?>&nbsp;<?php echo JText::_('COM_MINITEKWALL_HITS'); ?></p>
								</div>
							<?php } ?>
							
							<?php if ($this->detailBoxCount && isset($item->itemCount)) { ?>
								<div class="mnwall-count">
									<p><?php echo $item->itemCount; ?></p>
								</div>
							<?php } ?>
							
							<?php if ($this->detailBoxReadmore) { ?>
								<?php if (isset($item->itemLink)) { ?>
									<div class="mnwall-readmore">
										<a href="<?php echo $item->itemLink; ?>"><?php echo JText::_('COM_MINITEKWALL_READ_MORE'); ?></a>
									</div>
								<?php } ?>
							<?php } ?>
																												
						</div>
					
					<?php } ?>
															
				</div>
				
				<?php if ($this->hoverBox && ((! $this->scr_images || !isset($item->itemImage) || !$item->itemImage) || ($this->hoverBoxEffect == '2' || $this->hoverBoxEffect == '3'))) { ?>
													
					<div 
						class="mnwall-hover-box" 
						style=" 
						<?php echo $this->animated; ?>
						background-color: rgba(<?php echo $this->hb_bg_class; ?>,<?php echo $this->hb_bg_opacity_class; ?>);
						<?php if ($this->border_radius) { ?>
							border-radius: <?php echo $this->border_radius; ?>px;
						<?php } ?>
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
										<a href="<?php echo $item->itemImageRaw; ?>" class="fancybox mnwall-item-fancy-icon" rel="group">
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
	
	<?php }
	
} else {
	echo '-'; // =0 // for javascript purposes - Do not remove
}
