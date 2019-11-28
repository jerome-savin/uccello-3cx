<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Uccello\Core\Database\Migrations\Migration;
use Uccello\Core\Models\Module;
use Uccello\Core\Models\Domain;
use Uccello\Core\Models\Tab;
use Uccello\Core\Models\Block;
use Uccello\Core\Models\Field;
use Uccello\Core\Models\Filter;
use Uccello\Core\Models\Relatedlist;
use Uccello\Core\Models\Widget;
use Uccello\Core\Models\Link;

class CreateCallEventModule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->createTable();
        $module = $this->createModule();
        $this->activateModuleOnDomains($module);
        $this->createTabsBlocksFields($module);
        $this->createFilters($module);
        $this->createRelatedLists($module);
        $this->createLinks($module);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop table
        Schema::dropIfExists($this->tablePrefix . 'call-events');

        // Delete module
        Module::where('name', 'call-event')->forceDelete();
    }

    protected function initTablePrefix()
    {
        $this->tablePrefix = '';

        return $this->tablePrefix;
    }

    protected function createTable()
    {
        Schema::create($this->tablePrefix . 'call-events', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->unsignedInteger('contact_id')->nullable();
            $table->string('agent')->nullable();
            $table->string('duration')->nullable();
            $table->unsignedInteger('domain_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('domain_id')->references('id')->on('uccello_domains');

        });
    }

    protected function createModule()
    {
        $module = Module::create([
            'name' => 'call-event',
            'icon' => 'phone_in_talk',
            'model_class' => 'JeromeSavin\Uccello3cx\Models\CallEvent',
            'data' => json_decode('{"package":"jerome-savin\/uccello-3cx"}')
        ]);

        return $module;
    }

    protected function activateModuleOnDomains($module)
    {
        $domains = Domain::all();
        foreach ($domains as $domain) {
            $domain->modules()->attach($module);
        }
    }

    protected function createTabsBlocksFields($module)
    {
        // Tab tab.main
        $tab = Tab::create([
            'module_id' => $module->id,
            'label' => 'tab.main',
            'icon' => 'phone_in_talk',
            'sequence' => 0,
            'data' => null
        ]);

        // Block block.general
        $block = Block::create([
            'module_id' => $module->id,
            'tab_id' => $tab->id,
            'label' => 'block.general',
            'icon' => null,
            'sequence' => 0,
            'data' => null
        ]);

        // Field date
        Field::create([
            'module_id' => $module->id,
            'block_id' => $block->id,
            'name' => 'created_at',
            'uitype_id' => uitype('datetime')->id,
            'displaytype_id' => displaytype('detail')->id,
            'sequence' => 0,
            'data' => null
        ]);

        // Field type
        Field::create([
            'module_id' => $module->id,
            'block_id' => $block->id,
            'name' => 'type',
            'uitype_id' => uitype('text')->id,
            'displaytype_id' => displaytype('everywhere')->id,
            'sequence' => 1,
            'data' => json_decode('{"rules":"required"}')
        ]);

        // Field contact
        Field::create([
            'module_id' => $module->id,
            'block_id' => $block->id,
            'name' => 'contact',
            'uitype_id' => uitype('entity')->id,
            'displaytype_id' => displaytype('everywhere')->id,
            'sequence' => 2,
            'data' => json_decode('{"module":"contact"}')
        ]);

        // Field agent
        Field::create([
            'module_id' => $module->id,
            'block_id' => $block->id,
            'name' => 'agent',
            'uitype_id' => uitype('text')->id,
            'displaytype_id' => displaytype('everywhere')->id,
            'sequence' => 3,
            'data' => null
        ]);

        // Field duration
        Field::create([
            'module_id' => $module->id,
            'block_id' => $block->id,
            'name' => 'duration',
            'uitype_id' => uitype('text')->id,
            'displaytype_id' => displaytype('everywhere')->id,
            'sequence' => 4,
            'data' => null
        ]);

    }

    protected function createFilters($module)
    {
        // Filter
        Filter::create([
            'module_id' => $module->id,
            'domain_id' => null,
            'user_id' => null,
            'name' => 'filter.all',
            'type' => 'list',
            'columns' => [ 'created_at','type', 'contact', 'agent', 'duration' ],
            'conditions' => null,
            'order' => null,
            'is_default' => true,
            'is_public' => false,
            'data' => [ 'readonly' => true ]
        ]);

    }

    protected function createRelatedLists($module)
    {
        // Related List relatedlist.productList
        $relatedModule = Module::where('name', 'contact')->first();
        
        Relatedlist::create([
            'module_id' => $relatedModule->id,
            'related_module_id' => $module->id,
            'tab_id' => null,
            'related_field_id' => $module->fields->where('name', 'contact')->first()->id,
            'label' => 'relatedlist.callEvents',
            'type' => 'n-1',
            'method' => 'getDependentList',
            'data' => null,
            'sequence' => 0
        ]);

        // Add relatedlist widget
        $widget = Widget::where('label', 'widget.relatedlist')->first();
        $relatedlist = $relatedModule->relatedlists->where('label', 'relatedlist.callEvents')->first();
        $relatedModule->widgets()->attach($widget->id, ['data' => json_encode(['id' => $relatedlist->id]), 'sequence' => 1]);
    }

    protected function createLinks($module)
    {
    }
}