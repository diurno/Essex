<?php
/**
 * Esendex US Form
 *
 * @package       ESENDEXUSF
 * @author        500designs
 * @license       gplv2
 * @version       1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:   Esendex US Form
 * Plugin URI:    https://500designs.com
 * Description:   This is some demo short description...
 * Version:       1.0.0
 * Author:        500designs
 * Author URI:    https://500designs.com
 * Text Domain:   esendex-us-form
 * Domain Path:   /languages
 * License:       GPLv2
 * License URI:   https://www.gnu.org/licenses/gpl-2.0.html
 *
 * You should have received a copy of the GNU General Public License
 * along with Esendex US Form. If not, see <https://www.gnu.org/licenses/gpl-2.0.html/>.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
// Plugin name
define( 'ESENDEXUSF_NAME',			'Esendex US Form' );

// Plugin version
define( 'ESENDEXUSF_VERSION',		'1.0.0' );

// Plugin Root File
define( 'ESENDEXUSF_PLUGIN_FILE',	__FILE__ );

// Plugin base
define( 'ESENDEXUSF_PLUGIN_BASE',	plugin_basename( ESENDEXUSF_PLUGIN_FILE ) );

// Plugin Folder Path
define( 'ESENDEXUSF_PLUGIN_DIR',	plugin_dir_path( ESENDEXUSF_PLUGIN_FILE ) );

// Plugin Folder URL
define( 'ESENDEXUSF_PLUGIN_URL',	plugin_dir_url( ESENDEXUSF_PLUGIN_FILE ) );


define( 'ESENDEXUSF_LOG_SEPARATOR',	"=========================================================================");

define( 'ESENDEXUSF_API_KEY',"3a24f688-269d-445a-baea-9b9899d08c27");

/**
 * Load the main class for the core functionality
 */
require_once ESENDEXUSF_PLUGIN_DIR . 'core/class-esendex-us-form.php';

/**
 * The main function to load the only instance
 * of our master class.
 *
 * @author  500designs
 * @since   1.0.0
 * @return  object|Esendex_Us_Form
 */
function ESENDEXUSF() {
	return Esendex_Us_Form::instance();
}

ESENDEXUSF();
