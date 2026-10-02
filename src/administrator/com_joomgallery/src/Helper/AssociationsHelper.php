<?php
/**
 * *********************************************************************************
 *    @package    com_joomgallery                                                 **
 *    @author     JoomGallery::ProjectTeam <team@joomgalleryfriends.net>          **
 *    @copyright  2008 - 2026  JoomGallery::ProjectTeam                           **
 *    @license    GNU General Public License version 3 or later                   **
 * *********************************************************************************
 */

namespace Joomgallery\Component\Joomgallery\Administrator\Helper;

\defined('_JEXEC') || die;

use Joomla\CMS\Association\AssociationExtensionHelper;
use Joomla\CMS\Language\Associations;

/**
 * Association helper for JoomGallery content.
 *
 * Provides Joomla core multilingual association support for JoomGallery.
 *
 * @category JoomGallery
 * @package  Com_Joomgallery
 * @author   JoomGallery::ProjectTeam <team@joomgalleryfriends.net>
 * @license  GNU General Public License version 3 or later
 * @link     https://www.joomgalleryfriends.net
 * @since    4.5.0
 */
class AssociationsHelper extends AssociationExtensionHelper
{
    protected $extension = 'com_joomgallery';

    protected $itemTypes = ['category'];

    protected $associationsSupport = true;

    /**
     * Get the associations for a JoomGallery category.
     *
     * @param int         $id   The category ID.
     * @param string|null $view The view name.
     *
     * @return array  The associated categories.
     *
     * @since 4.5.0
     */
    public function getAssociationsForItem($id = 0, $view = null)
    {
        return $this->getAssociations('category', $id);
    }

    /**
     * Get the associations for a category.
     *
     * @param string $typeName The association type.
     * @param int    $id       The category ID.
     *
     * @return array  The associated categories or an empty array.
     *
     * @since 4.5.0
     */
    public function getAssociations($typeName, $id)
    {
        if ($typeName !== 'category') {
            return [];
        }

        // Use Joomla's core association system for multilingual content.
        return Associations::getAssociations(
            $this->extension,
            '#__joomgallery_categories',
            'com_joomgallery.category',
            $id,
            'id',
            '',
            ''
        );
    }

    /**
     * Get the association type configuration.
     *
     * @param string $typeName The association type.
     *
     * @return array  The association type configuration.
     *
     * @since 4.5.0
     */
    public function getType($typeName = '')
    {
        if ($typeName !== 'category') {
            return [
              'fields'  => [],
              'support' => [],
              'tables'  => [],
              'joins'   => [],
              'title'   => '',
            ];
        }

        // Build the configuration from Joomla's default association templates.
        $fields  = $this->getFieldsTemplate();
        $support = $this->getSupportTemplate();

        $fields['state'] = 'a.published';

        $support['state']    = true;
        $support['acl']      = true;
        $support['checkout'] = true;

        return [
          'fields'  => $fields,
          'support' => $support,
          'tables'  => [
            'a' => '#__joomgallery_categories',
          ],
          'joins' => [],
          'title' => 'category',
        ];
    }
}