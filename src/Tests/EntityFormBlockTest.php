<?php
/**
 * @file
 * Contains \Drupal\entityform_block\EntityFormBlockTests.
 */

namespace Drupal\entityform_block\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests the entity form blocks.
 *
 * @group entityform_block
 */
class EntityFormBlockTest extends WebTestBase {

  /**
   * Disabled config schema checking temporarily until all errors are resolved.
   */
  protected $strictConfigSchema = FALSE;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array(
    'node',
    'block',
    'entityform_block',
    'taxonomy'
  );

  /**
   * Tests the entity form blocks.
   */
  public function testEntityFormBlock() {
    // Create article content type.
    $this->drupalCreateContentType(array('type' => 'article', 'name' => 'Article'));

    $admin_user = $this->drupalCreateUser(array(
      'administer blocks',
      'administer nodes',
      'administer site configuration',
      'create article content',
      'administer taxonomy',
      // Needed for create user form.
      // @todo Support register.
      'administer users',
    ));
    $this->drupalLogin($admin_user);

    // Add a content block with an entity form.
    $this->drupalGet('admin/structure/block');
    $this->clickLink(t('Entity form'));
    $edit = array(
      'settings[entity_type_bundle]' => 'node.article',
      'region' => 'content',
    );
    $this->drupalPostForm(NULL, $edit, t('Save block'));

    $this->drupalGet('<front>');

    // Make sure the entity form is available.
    $this->assertText('Entity form');
    $this->assertField('title[0][value]');
    $this->assertField('body[0][value]');
    $this->assertField('revision');

    // Add a vocabulary.
    $this->drupalGet('admin/structure/taxonomy/add');
    $edit = array(
      'vid' => 'vocabulary_tags',
      'name' => 'Tags',
    );
    $this->drupalPostForm(NULL, $edit, t('Save'));

    // Add a form block for creating tags.
    $this->drupalGet('admin/structure/block');
    $this->clickLink(t('Entity form'));
    $edit = array(
      'settings[entity_type_bundle]' => 'taxonomy_term.vocabulary_tags',
      'region' => 'content',
    );
    $this->drupalPostForm(NULL, $edit, t('Save block'));

    $this->drupalGet('<front>');

    // Make sure the vocabulary form is available.
    $this->assertField('name[0][value]');
    $this->assertField('description[0][value]');

    // Add a form block for users.
    $this->drupalGet('admin/structure/block');
    $this->clickLink(t('Entity form'));
    $edit = array(
      'settings[entity_type_bundle]' => 'user.user',
      'region' => 'content',
    );
    $this->drupalPostForm(NULL, $edit, t('Save block'));

    $this->drupalGet('<front>');

    // Make sure the user form is available.
    $this->assertField('mail');
    $this->assertField('name');
    $this->assertField('pass[pass1]');
  }

}
