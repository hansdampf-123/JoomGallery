<?php
/**
 * *********************************************************************************
 *    @package    com_joomgallery
 * *********************************************************************************
 */

\defined('_JEXEC') || die;

use Joomgallery\Component\Joomgallery\Administrator\Helper\JoomHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;

extract($displayData);

/**
 * load tags
 */
function jgGetImageTags(int $imgid): array
{
    $db = Factory::getDbo();

    $query = $db->getQuery(true)
        ->select('t.title')
        ->from($db->quoteName('#__joomgallery_tags', 't'))
        ->join(
            'INNER',
            $db->quoteName('#__joomgallery_tags_ref', 'r')
            . ' ON r.tagid = t.id'
        )
        ->where('r.imgid = ' . (int) $imgid)
        ->order('t.title ASC');

    $db->setQuery($query);

    return $db->loadColumn() ?: [];
}
?>

<div class="jg-gallery <?php echo $layout; ?>" itemscope itemtype="https://schema.org/ImageGallery">
  <div class="jg-loader"></div>

  <div id="lightgallery-<?php echo $id; ?>"
       class="jg-images <?php echo $layout; ?>-<?php echo $num_columns; ?> jg-category"
       data-masonry="{ pollDuration: 175 }">

    <?php $index = 0; ?>
    <?php foreach($items as $key => $item) : ?>

      <?php
        $tags = jgGetImageTags((int) $item->id);
      ?>

      <div class="jg-image">
        <div class="jg-image-thumbnail<?php if($image_class && $layout != 'justified') : ?> boxed<?php endif; ?>">

          <?php if($layout != 'justified') : ?>
            <div class="jg-image-caption-hover <?php echo $caption_align; ?>">
          <?php endif; ?>

          <?php if($image_link == 'lightgallery') : ?>

            <a class="lightgallery-item"
               href="<?php echo JoomHelper::getImg($item, $lightbox_type); ?>"
               data-sub-html="#jg-image-caption-<?php echo $item->id; ?>"
               data-thumb="<?php echo JoomHelper::getImg($item, $image_type); ?>">

              <img src="<?php echo JoomHelper::getImg($item, $image_type); ?>"
                   class="jg-image-thumb"
                   alt="<?php echo $item->title; ?>"
                   itemprop="image"
                   itemscope
                   itemtype="https://schema.org/image"
                   <?php if($layout != 'justified') : ?> loading="lazy"<?php endif; ?>>

              <?php if($image_title && $layout == 'justified') : ?>
                <div class="jg-image-caption-hover <?php echo $caption_align; ?>">
                  <?php echo $this->escape($item->title); ?>
                </div>
              <?php endif; ?>

            </a>

            <?php if($image_title || $image_desc) : ?>
              <div id="jg-image-caption-<?php echo $item->id; ?>" style="display:none">

                <?php if($image_title) : ?>
                  <div class="jg-image-caption <?php echo $caption_align; ?>">
                    <?php
                      $caption = $this->escape($item->title);
                      if (!empty($tags)) {
                        $caption .= ' | ' . implode(' | ', array_map('htmlspecialchars', $tags));
                      }
                      echo $caption;
                    ?>
                  </div>
                <?php endif; ?>

                <?php if($image_desc) : ?>
                  <div class="jg-image-desc <?php echo $caption_align; ?>">
                    <?php echo $item->description; ?>
                  </div>
                <?php endif; ?>

              </div>
            <?php endif; ?>

          <?php elseif($image_link == 'defaultview') : ?>

            <a href="<?php echo Route::_(JoomHelper::getViewRoute('image', (int)$item->id, (int)$item->catid)); ?>">
              <img src="<?php echo JoomHelper::getImg($item, $image_type); ?>"
                   class="jg-image-thumb"
                   alt="<?php echo $item->title; ?>"
                   itemprop="image"
                   itemscope
                   itemtype="https://schema.org/image"
                   <?php if($layout != 'justified') : ?> loading="lazy"<?php endif; ?>>
            </a>

          <?php else : ?>

            <img src="<?php echo JoomHelper::getImg($item, $image_type); ?>"
                 class="jg-image-thumb"
                 alt="<?php echo $item->title; ?>"
                 itemprop="image"
                 itemscope
                 itemtype="https://schema.org/image"
                 <?php if($layout != 'justified') : ?> loading="lazy"<?php endif; ?>>

          <?php endif; ?>

          <?php if($layout != 'justified') : ?>
            </div>
          <?php endif; ?>

        </div>

        <?php if($layout != 'justified') : ?>
          <div class="jg-image-caption <?php echo $caption_align; ?>">

            <?php if($image_title) : ?>

              <?php if($title_link == 'lightgallery' && $image_link != 'lightgallery') : ?>

                <a class="lightgallery-item"
                   href="<?php echo JoomHelper::getImg($item, $lightbox_type); ?>"
                   data-sub-html="#jg-image-caption-<?php echo $item->id; ?>"
                   data-thumb="<?php echo JoomHelper::getImg($item, $image_type); ?>">
                  <?php echo $this->escape($item->title); ?>
                </a>

                <div id="jg-image-caption-<?php echo $item->id; ?>" style="display:none">

                  <?php if($image_title) : ?>
                    <div class="jg-image-caption <?php echo $caption_align; ?>">
                      <?php
                        $caption = $this->escape($item->title);
                        if (!empty($tags)) {
                          $caption .= ' | ' . implode(' | ', array_map('htmlspecialchars', $tags));
                        }
                        echo $caption;
                      ?>
                    </div>
                  <?php endif; ?>

                  <?php if($image_desc) : ?>
                    <div class="jg-image-desc <?php echo $caption_align; ?>">
                      <?php echo $item->description; ?>
                    </div>
                  <?php endif; ?>

                </div>

              <?php else : ?>

                <?php if($title_link == 'defaultview') : ?>
                  <a href="<?php echo Route::_(JoomHelper::getViewRoute('image', (int)$item->id, (int)$item->catid)); ?>">
                    <?php echo $this->escape($item->title); ?>
                  </a>
                <?php else : ?>
                  <?php echo $this->escape($item->title); ?>
                <?php endif; ?>

              <?php endif; ?>

            <?php endif; ?>

            <?php if($image_desc) : ?>
              <div><?php echo $item->description; ?></div>
            <?php endif; ?>

            <?php if($image_date) : ?>
              <div><?php echo Text::_('COM_JOOMGALLERY_DATE') . ': ' . HTMLHelper::_('date', $item->date, Text::_('DATE_FORMAT_LC6')); ?></div>
            <?php endif; ?>

            <?php if($image_author) : ?>
              <div><?php echo Text::_('JAUTHOR') . ': ' . $this->escape($item->author); ?></div>
            <?php endif; ?>

          </div>
        <?php endif; ?>

      </div>

    <?php $index++; endforeach; ?>

  </div>
</div>
