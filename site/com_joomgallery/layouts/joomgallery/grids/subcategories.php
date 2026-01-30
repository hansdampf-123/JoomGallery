<?php
/**
 * *********************************************************************************
 *    @package    com_joomgallery                                                 **
 *    @author     JoomGallery::ProjectTeam <team@joomgalleryfriends.net>          **
 *    @copyright  2008 - 2025  JoomGallery::ProjectTeam                           **
 *    @license    GNU General Public License version 3 or later                   **
 * *********************************************************************************
 */

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') || die;
// phpcs:enable PSR1.Files.SideEffects

use Joomgallery\Component\Joomgallery\Administrator\Helper\JoomHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

extract($displayData);

/**
 * Layout variables
 * -----------------
 * @var string $layout
 * @var array  $items
 * @var int    $num_columns
 * @var string $image_type
 * @var string $image_class
 * @var string $caption_align
 * @var string $description
 * @var bool   $random_image
 */

/**
 * Counts all pictures in categories and their subcategories
 *
 * @param int $catId
 * @return int
 */
function getTotalImagesInCategory($catId)
{
  $db = Factory::getDbo();

  $catTable = $db->quoteName('#__joomgallery_categories');
  $imgTable = $db->quoteName('#__joomgallery');

  // Get all published categories
  $query = $db->getQuery(true)
    ->select('id, lft, rgt')
    ->from($catTable)
    ->where('published = 1');

  $db->setQuery($query);
  $cats = $db->loadAssocList('id');

  if (!isset($cats[$catId])) {
    return 0;
  }

  $lft = $cats[$catId]['lft'];
  $rgt = $cats[$catId]['rgt'];

  $idsToCount = [];

  foreach ($cats as $id => $cat) {
    if ($cat['lft'] >= $lft && $cat['rgt'] <= $rgt) {
      $idsToCount[] = (int) $id;
    }
  }

  if (!$idsToCount) {
    return 0;
  }

  // Count images
  $query = $db->getQuery(true)
    ->select('COUNT(*)')
    ->from($imgTable)
    ->where('catid IN (' . implode(',', $idsToCount) . ')')
    ->where('published = 1');

  $db->setQuery($query);

  return (int) $db->loadResult();
}
?>

<div class="jg-gallery" itemscope itemtype="https://schema.org/ImageGallery">
  <div class="jg-loader"></div>

  <div class="jg-images <?php echo $layout; ?>-<?php echo $num_columns; ?> jg-subcategories"
       data-masonry='{ "pollDuration": 175 }'>

    <?php foreach ($items as $item) : ?>
      <?php
        $img_type = $image_type;

        if ($item->thumbnail == 0 && $random_image) {
          $item->thumbnail = $item->id;
          $img_type = 'rnd_cat:' . $image_type;
        }

        $count = getTotalImagesInCategory($item->id);

        if ($count === 1) {
          $label = Text::_('COM_JOOMGALLERY_IMAGE');
        } else {
          $label = Text::_('COM_JOOMGALLERY_IMAGES');
        }
      ?>

      <div class="jg-image">
        <div class="jg-image-thumbnail<?php echo ($image_class && $layout !== 'justified') ? ' boxed' : ''; ?>">
          <a href="<?php echo Route::_(JoomHelper::getViewRoute('category', (int) $item->id)); ?>">
            <img
              src="<?php echo JoomHelper::getImg($item->thumbnail, $img_type); ?>"
              class="jg-image-thumb"
              alt="<?php echo $this->escape($item->title); ?>"
              itemprop="image"
              itemscope
              itemtype="https://schema.org/image"
              <?php if ($layout !== 'justified') : ?>loading="lazy"<?php endif; ?>
            >

            <?php if ($layout === 'justified') : ?>
              <div class="jg-image-caption-hover <?php echo $caption_align; ?>">
                <?php echo $this->escape($item->title); ?>
              </div>
            <?php endif; ?>
          </a>
        </div>

        <?php if ($layout !== 'justified') : ?>
          <div class="jg-image-caption <?php echo $caption_align; ?>">
            <a class="jg-link"
               href="<?php echo Route::_(JoomHelper::getViewRoute('category', (int) $item->id)); ?>">
              <?php echo $this->escape($item->title); ?>
            </a>
            <br>
            <div class="numberofimages">
              (<?php echo $count . ' ' . $label; ?>)
            </div>

            <?php if ($description) : ?>
              <?php echo $item->description; ?>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>

    <?php endforeach; ?>
  </div>
</div>
