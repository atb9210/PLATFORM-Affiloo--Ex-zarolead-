<!-- =========================================================================================
    File Name: AgGridTable.vue
    Description: Ag Grid table
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="Products" code-toggler>

        <p class="flex items-center mb-4">
            <vs-button v-if="canCreateAndEdit" class="mr-2" color="primary" type="filled" icon-pack="feather"
                :icon="(mode == 'view') ? 'icon-plus' : 'icon-arrow-left'"
                @click="(mode == 'view') ? beginCreation() : (mode = 'view')"></vs-button>
            <vs-button v-if="canCreateAndEdit && mode == 'edit'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-settings" @click="showSettings()"></vs-button>
            <vs-button v-if="canCreateAndEdit" class="mr-2" color="danger" type="filled" icon-pack="feather" icon="icon-trash" @click="confirmDeletePopup"></vs-button>
        </p>

        <vs-dropdown vs-trigger-click class="cursor-pointer">
            <div class="p-4 border border-solid d-theme-border-grey-light rounded-full d-theme-dark-bg cursor-pointer flex items-center justify-between font-medium">
                <span class="mr-2">{{ currentPage * maxItems - (maxItems - 1) }} - {{ objects.length - currentPage * maxItems > 0 ? currentPage * maxItems : objects.length }} of {{ objects.length }}</span>
                <feather-icon icon="ChevronDownIcon" svgClasses="h-4 w-4" />
            </div>
            <vs-dropdown-menu>
                <vs-dropdown-item @click="maxItems = 20">
                    <span>20</span>
                </vs-dropdown-item>
                <vs-dropdown-item @click="maxItems = 50">
                    <span>50</span>
                </vs-dropdown-item>
                <vs-dropdown-item @click="maxItems = 100">
                    <span>100</span>
                </vs-dropdown-item>
                <vs-dropdown-item @click="maxItems = 150">
                    <span>150</span>
                </vs-dropdown-item>
            </vs-dropdown-menu>
        </vs-dropdown>

        <vue-context ref="context_menu" v-slot="{ data }">
            <li>
              <a href="#" @click="$refs.context_menu.close();editForm(data.id);" class="flex items-center text-sm">
                <feather-icon icon="EditIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Edit</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.context_menu.close();copyCheckoutLink(data.id);" class="flex items-center text-sm">
                <feather-icon icon="CopyIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Copy Checkout Link</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.context_menu.close();deleteObject(data.id);" class="flex items-center text-sm">
                <feather-icon icon="TrashIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Delete</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.context_menu.close()" class="flex items-center text-sm">
                <feather-icon icon="XIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Close</span>
              </a>
            </li>
        </vue-context>

        <vs-table v-if="mode == 'view'" stripe multiple v-model="selected" :max-items="maxItems" pagination search :data="objects" :currentPage="currentPage" @change-page="onPageChanged">

            <template slot="thead">
                <vs-th sort-key="id">Id</vs-th>
                <vs-th sort-key="name">Name</vs-th>
                <vs-th sort-key="advertiser_name">Advertiser</vs-th>
                <vs-th sort-key="tag.name">Tag</vs-th>
                <vs-th sort-key="country">Country</vs-th>
                <vs-th sort-key="sku" v-if="isAdmin">SKU</vs-th>
                <vs-th sort-key="cost" v-if="isAdmin">Cost</vs-th>
                <vs-th sort-key="price" v-if="isAdmin">Price</vs-th>
                <vs-th sort-key="stock_quantity" v-if="isAdmin">Stock Q.ty</vs-th>
                <vs-th sort-key="url" v-if="isAdmin">Creativity</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                    <vs-td class="column-no-padding" :data="data[indextr].id">
                        {{data[indextr].id}}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].name">
                        <div @contextmenu.prevent="$refs.context_menu.open($event, data[indextr])">
                            <vs-chip>
                                {{data[indextr].name}}
                            </vs-chip>
                        </div>
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].advertiser_name">
                        {{data[indextr].advertiser_name}}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].tag.name">
                        {{data[indextr].tag.name}}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].country">
                        {{data[indextr].country}}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].sku">
                        {{data[indextr].sku}}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].cost">
                        € {{data[indextr].cost}}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].price">
                        € {{data[indextr].price}}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].stock_quantity">
                        {{data[indextr].stock_quantity}}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].url">
                        <a :href="data[indextr].url">Creativity</a>
                    </vs-td>

                </vs-tr>
            </template>
        </vs-table>

        <!-- Product create -->
        <div class="vx-row" v-if="mode == 'create'">
            <div class="vx-col md:w-1/2 w-full">
                <vs-input class="w-full" label="Name" icon-pack="feather" icon="icon-tag" icon-no-border v-model="newObject['create'].name" />
                <span class="text-danger">{{ errors.first('create.name') }}</span>
            </div>

            <div class="vx-col md:w-1/2 w-full">
                <vs-select v-model="newObject['create'].tag" class="w-full select-large" label="Tag" v-validate="'required'">
                    <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in tags" class="w-full" />
                </vs-select>
                <span class="text-danger">{{ errors.first('create.tag_id') }}</span>
            </div>

            <div class="vx-col md:w-1/2 w-full">
                <vs-select v-model="newObject['create'].advertiser" class="w-full select-large" label="Advertiser" v-validate="'required'">
                    <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in advertisers" class="w-full" />
                </vs-select>
                <span class="text-danger">{{ errors.first('create.advertiser_id') }}</span>
            </div>

            <div class="vx-col md:w-1/2 w-full">
                <vs-input class="w-full" label="Country" icon-pack="feather" icon="icon-globe" icon-no-border v-model="newObject['create'].country" />
                <span class="text-danger">{{ errors.first('create.country') }}</span>
            </div>

            <vs-list class="vx-col w-full mt-5">
                <vs-list-header icon-pack="feather" icon="icon-hash" title="Quantity Options" ></vs-list-header>
                <vs-list-item
                    icon-pack="feather" icon="icon-check"
                    :title="'Quantity: ' + quantityOption.quantity"
                    :subtitle="'Price: €' + quantityOption.price + ', Commission: €' + quantityOption.commission + ', Sub Commission: €' + quantityOption.sub_commission"
                    v-for="(quantityOption, index) in quantityOptions" :key="index">
                    <div class="flex items-center">
                        <vs-radio class="mr-4" v-model="quantityDefaultIndex" :vs-value="index" @change="changeQuantityDefault">Default</vs-radio>
                        <vs-button class="mr-2" icon-pack="feather" icon="icon-edit" type="filled" color="primary" @click="showEditQuantityOptionForm(index)"></vs-button>
                        <vs-button class="mr-2" icon-pack="feather" icon="icon-trash" type="filled" color="danger" @click="removeQuantityOption(index)"></vs-button>
                    </div>
                </vs-list-item>
            </vs-list>
            <div class="vx-col w-full mt-2">
                <vs-button class="mt-3" color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="showForm('create_qty')">Add option</vs-button> 
            </div>

            <hr class="w-full mt-2 mb-2">

            <h2 class="vx-col w-full">Optional fields</h2>

            <!--<div class="vx-col w-full">
                <vs-input class="w-full" label="URL image" icon-pack="feather" icon="icon-image" icon-no-border v-model="newObject['create'].info['image']" />
            </div>-->

            <div class="vx-col w-full flex items-center">
                <img :src="createProductImagePreview" />
            </div>

            <div class="vx-col w-full flex items-center">
                <div>
                    <vs-button color="primary" type="filled" icon-pack="feather" icon="icon-image" class="w-full" @click="showFilePicker()">Upload image</vs-button>
                </div>
                <div>
                    <span class="ml-auto mr-auto">{{ (fileToUpload != null) ? fileToUpload.name : 'No file selected' }}</span>
                </div>
                <input type="file" ref="fileUpload" class="hidden" accept="image/*" @change="onFilesUploadChange">
            </div>

            <div class="vx-col w-full">
                <vs-input class="w-full" label="Descrizione" icon-pack="feather" icon="icon-hash" icon-no-border v-model="newObject['create'].info['description']" />
            </div>

            <div class="vx-col md:w-1/2 w-full">
                <vs-input class="w-full" label="SKU" icon-pack="feather" icon="icon-tag" icon-no-border v-model="newObject['create'].info['sku']" />
            </div>

            <div class="vx-col md:w-1/2 w-full">
                <vs-input type="number" min="0.0" step="0.01" class="w-full" label="Cost" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="newObject['create'].info['cost']" />
            </div>

            <div class="vx-col md:w-1/2 w-full">
                <vs-input type="number" min="0" step="1" class="w-full" label="Stock quantity" icon-pack="feather" icon="icon-box" icon-no-border v-model="newObject['create'].info['stock_quantity']" />
            </div>

            <div class="vx-col md:w-1/2 w-full">
                <vs-input class="w-full" label="Size (WxWxH)" icon-pack="feather" icon="icon-package" icon-no-border v-model="newObject['create'].info['size']" />
            </div>

            <div class="vx-col md:w-1/2 w-full">
                <vs-input type="number" min="0.0" step="0.01" class="w-full" label="VAT %" icon-pack="feather" icon="icon-alert-octagon" icon-no-border v-model="newObject['create'].info['vat']" />
            </div>

            <div class="vx-col md:w-1/2 w-full">
                <vs-input class="w-full" label="URL" icon-pack="feather" icon="icon-globe" icon-no-border v-model="newObject['create'].info['url']" />
            </div>

            <div class="vx-col w-full">
                <span class="text-danger">{{ errors.first('create.info') }}</span>
            </div>

            <div class="vx-col w-full flex items-center">
                <vs-button class="mt-12 mr-auto ml-auto" @click="createObject" color="primary" type="filled" :disabled="!isValidForm('create')">Create</vs-button>
            </div>
        </div>

        <!-- Product edit -->
        <div class="vx-row" v-if="mode == 'edit'">
            <div class="vx-col md:w-1/2 w-full">
                <vs-input class="w-full" label="Name" icon-pack="feather" icon="icon-tag" icon-no-border v-model="newObject['edit'].name" />
                <span class="text-danger">{{ errors.first('edit.name') }}</span>
            </div>

            <div class="vx-col md:w-1/2 w-full">
                <vs-select v-model="newObject['edit'].tag" class="w-full select-large" label="Tag" v-validate="'required'">
                    <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in tags" class="w-full" />
                </vs-select>
                <span class="text-danger">{{ errors.first('edit.tag_id') }}</span>
            </div>

            <div class="vx-col md:w-1/2 w-full">
                <vs-select v-model="newObject['edit'].advertiser" class="w-full select-large" label="Advertiser" v-validate="'required'">
                    <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in advertisers" class="w-full" />
                </vs-select>
                <span class="text-danger">{{ errors.first('edit.advertiser_id') }}</span>
            </div>

            <div class="vx-col md:w-1/2 w-full">
                <vs-input class="w-full" label="Country" icon-pack="feather" icon="icon-globe" icon-no-border v-model="newObject['edit'].country" />
                <span class="text-danger">{{ errors.first('edit.country') }}</span>
            </div>

            <vs-list class="vx-col w-full mt-5">
                <vs-list-header icon-pack="feather" icon="icon-hash" title="Quantity Options" ></vs-list-header>
                <vs-list-item
                    icon-pack="feather" icon="icon-check"
                    :title="'Quantity: ' + quantityOption.quantity"
                    :subtitle="'Price: €' + quantityOption.price + ', Commission: €' + quantityOption.commission + ', Sub Commission: €' + quantityOption.sub_commission"
                    v-for="(quantityOption, index) in quantityOptions" :key="index">
                    <div class="flex items-center">
                        <vs-radio class="mr-4" v-model="quantityDefaultIndex" :vs-value="index" @change="changeQuantityDefault">Default</vs-radio>
                        <vs-button class="mr-2" icon-pack="feather" icon="icon-edit" type="filled" color="primary" @click="showEditQuantityOptionForm(index)"></vs-button>
                        <vs-button class="mr-2" icon-pack="feather" icon="icon-trash" type="filled" color="danger" @click="removeQuantityOption(index)"></vs-button>
                    </div>
                </vs-list-item>
            </vs-list>
            <div class="vx-col w-full mt-2">
                <vs-button class="mt-3" color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="showForm('create_qty')">Add option</vs-button> 
            </div>

            <hr class="w-full mt-2 mb-2">

            <h2 class="vx-col w-full">Optional fields</h2>

            <!--<div class="vx-col w-full">
                <vs-input class="w-full" label="URL image" icon-pack="feather" icon="icon-image" icon-no-border v-model="newObject['edit'].info['image']" />
            </div>-->

            <div class="vx-col w-full flex items-center">
                <img :src="editProductImagePreview" />
            </div>

            <div class="vx-col w-full flex items-center">
                <div>
                    <vs-button color="primary" type="filled" icon-pack="feather" icon="icon-image" class="w-full" @click="showFilePicker()">Upload image</vs-button>
                </div>
                <div>
                    <span class="ml-auto mr-auto">{{ (fileToUpload != null) ? fileToUpload.name : 'No file selected' }}</span>
                </div>
                <input type="file" ref="fileUpload" class="hidden" accept="image/*" @change="onFilesUploadChange">
            </div>

            <div class="vx-col w-full">
                <vs-input class="w-full" label="Descrizione" icon-pack="feather" icon="icon-hash" icon-no-border v-model="newObject['edit'].info['description']" />
            </div>

            <div class="vx-col md:w-1/2 w-full">
                <vs-input class="w-full" label="SKU" icon-pack="feather" icon="icon-tag" icon-no-border v-model="newObject['edit'].info['sku']" />
            </div>

            <div class="vx-col md:w-1/2 w-full">
                <vs-input type="number" min="0.0" step="0.01" class="w-full" label="Cost" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="newObject['edit'].info['cost']" />
            </div>

            <div class="vx-col md:w-1/2 w-full">
                <vs-input type="number" min="0" step="1" class="w-full" label="Stock quantity" icon-pack="feather" icon="icon-box" icon-no-border v-model="newObject['edit'].info['stock_quantity']" />
            </div>

            <div class="vx-col md:w-1/2 w-full">
                <vs-input class="w-full" label="Size (WxWxH)" icon-pack="feather" icon="icon-package" icon-no-border v-model="newObject['edit'].info['size']" />
            </div>

            <div class="vx-col md:w-1/2 w-full">
                <vs-input type="number" min="0.0" step="0.01" class="w-full" label="VAT %" icon-pack="feather" icon="icon-alert-octagon" icon-no-border v-model="newObject['edit'].info['vat']" />
            </div>

            <div class="vx-col md:w-1/2 w-full">
                <vs-input class="w-full" label="URL" icon-pack="feather" icon="icon-globe" icon-no-border v-model="newObject['edit'].info['url']" />
            </div>

            <div class="vx-col w-full">
                <span class="text-danger">{{ errors.first('edit.info') }}</span>
            </div>

            <div class="vx-col w-full flex items-center">
                <vs-button class="mt-12 mr-auto ml-auto" @click="updateObject" color="primary" type="filled" :disabled="!isValidForm('edit')">Update</vs-button>
            </div>
        </div>

        <!-- Create quantity option popup -->
        <vs-popup title="Create Quantity Option" :active.sync="showPopup['create_qty']">
            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Quantity</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-hash" icon-no-border v-model="newObject['create_qty'].quantity" />
                </div>
            </div>
            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Title</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="newObject['create_qty'].title" />
                </div>
            </div>
            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Price</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="number" step="0.01" min="0" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="newObject['create_qty'].price" />
                </div>
            </div>
            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Commission</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="number" step="0.01" min="0" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="newObject['create_qty'].commission" />
                </div>
            </div>
            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Sub Commission</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="number" step="0.01" min="0" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="newObject['create_qty'].sub_commission" />
                </div>
            </div>
            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Old price</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="number" step="0.01" min="0" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="newObject['create_qty'].old_price" />
                </div>
            </div>
            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Discount %</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="number" step="0.01" min="0" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="newObject['create_qty'].discount" />
                </div>
            </div>
            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="createQuantityOption" color="primary" type="filled" :disabled="!isValidForm('create_qty')">Add</vs-button>
            </div>
        </vs-popup>

        <!-- Edit quantity option popup -->
        <vs-popup title="Edit Quantity Option" :active.sync="showPopup['edit_qty']">
            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Quantity</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-hash" icon-no-border v-model="newObject['edit_qty'].quantity" />
                </div>
            </div>
            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Title</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="newObject['edit_qty'].title" />
                </div>
            </div>
            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Price</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="number" step="0.01" min="0" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="newObject['edit_qty'].price" />
                </div>
            </div>
            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Commission</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="number" step="0.01" min="0" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="newObject['edit_qty'].commission" />
                </div>
            </div>
            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Sub Commission</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="number" step="0.01" min="0" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="newObject['edit_qty'].sub_commission" />
                </div>
            </div>
            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Old price</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="number" step="0.01" min="0" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="newObject['edit_qty'].old_price" />
                </div>
            </div>
            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Discount %</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="number" step="0.01" min="0" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="newObject['edit_qty'].discount" />
                </div>
            </div>
            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="editQuantityOption" color="primary" type="filled" :disabled="!isValidForm('edit_qty')">Update</vs-button>
            </div>
        </vs-popup>


        <!-- Checkout settings popup -->
        <vs-popup title="Checkout form settings" :active.sync="showPopup['checkout_settings']">

            <!-- Privacy policy -->
            <div class="vx-col w-full mt-5">
                <div class="mb-2">Privacy policy</div>
                <vx-input-group prependClasses="border border-solid border-grey border-r-0">
                    <template slot="prepend">
                        <div class="prepend-text">
                            <vs-checkbox v-model="plugins['privacy_policy'].enabled"></vs-checkbox>
                        </div>
                    </template>
                    <vs-input
                        class="w-full"
                        icon-pack="feather" icon="icon-unlock" icon-no-border
                        v-model="plugins['privacy_policy'].url"
                        placeholder="Privacy Policy URL"
                    />
                </vx-input-group>
            </div>

            <!-- Upsell plugin -->
            <div class="vx-col w-full mt-5">
                <div class="mb-2">Upsell</div>
                <vx-input-group prependClasses="border border-solid border-grey border-r-0">
                    <template slot="prepend">
                        <div class="prepend-text">
                            <vs-checkbox v-model="plugins['upsell'].enabled"></vs-checkbox>
                        </div>
                    </template>
                    <vs-select v-model="plugins['upsell'].best_seller" class="w-full">
                        <vs-select-item :key="index" :value="item" :text="item" v-for="(item,index) in upSellOptions" class="w-full" />
                    </vs-select>
                </vx-input-group>
            </div>

            <!-- Facebook conversion API plugin -->
            <div class="vx-col w-full mt-5">
                <div class="mb-2">Facebook Conversions API</div>
                <vx-input-group prependClasses="border border-solid border-grey border-r-0">
                    <template slot="prepend">
                        <div class="prepend-text">
                            <vs-checkbox v-model="plugins['facebook'].enabled"></vs-checkbox>
                        </div>
                    </template>
                    <vs-input
                        class="w-full"
                        icon-pack="feather" icon="icon-facebook" icon-no-border
                        v-model="plugins['facebook'].pixel"
                        placeholder="Facebook pixel"
                    />
                    <vs-input
                        class="w-full"
                        icon-pack="feather" icon="icon-facebook" icon-no-border
                        v-model="plugins['facebook'].access_token"
                        placeholder="Access token"
                    />
                    <!-- TODO: This is only for testing -->
                    <vs-input
                        class="w-full"
                        icon-pack="feather" icon="icon-facebook" icon-no-border
                        v-model="plugins['facebook'].test_code"
                        placeholder="Test code (e.g. TEST2331)"
                    />
                    <vs-input
                        type="number"
                        step="0.01"
                        min="0"
                        class="w-full"
                        icon-pack="feather" icon="icon-dollar-sign" icon-no-border
                        v-model="plugins['facebook'].eventValue"
                        name="fb_event_value" placeholder="Conversion event value"
                    />
                    <vs-select v-model="plugins['facebook'].eventCurrency" class="w-full select-large">
                        <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in facebookEventCurrencyOptions" class="w-full" />
                    </vs-select>
                </vx-input-group>
            </div>

            <!-- TikTok pixel plugin -->
            <div class="vx-col w-full mt-5">
                <div class="mb-2">TikTok Pixel</div>
                <vx-input-group prependClasses="border border-solid border-grey border-r-0">
                    <template slot="prepend">
                        <div class="prepend-text">
                            <vs-checkbox v-model="plugins['tiktok'].enabled"></vs-checkbox>
                        </div>
                    </template>
                    <vs-input
                        class="w-full"
                        icon-pack="feather" icon="icon-music" icon-no-border
                        v-model="plugins['tiktok'].pixel"
                        placeholder="TikTok pixel"
                    />
                    <vs-input
                        class="w-full"
                        icon-pack="feather" icon="icon-music" icon-no-border
                        v-model="plugins['tiktok'].token"
                        placeholder="TikTok pixel token API"
                    />
                    <vs-select v-model="plugins['tiktok'].eventName" class="w-full select-large">
                        <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in tiktokEventNameOptions" class="w-full" />
                    </vs-select>
                </vx-input-group>
            </div>

            <hr class="mb-2 mt-2">

            <!-- Payment methods -->
            <h2 class="mb-2">Payment methods</h2>

            <div class="vx-col w-full mt-5">
                <div class="mb-2">Cash on delivery</div>
                <vx-input-group prependClasses="border border-solid border-grey border-r-0">
                    <template slot="prepend">
                        <div class="prepend-text">
                            <vs-checkbox v-model="paymentMethods['paymentMethodCod'].enabled"></vs-checkbox>
                        </div>
                    </template>
                    <vs-input
                        class="w-full"
                        icon-pack="feather" icon="icon-dollar-sign" icon-no-border
                        v-model="paymentMethods['paymentMethodCod'].fee"
                        placeholder="Fee"
                        type="number"
                        min="0"
                        step="0.1"
                    />
                </vx-input-group>
            </div>

            <div class="vx-col w-full mt-5">
                <div class="mb-2">Online (credit card)</div>
                <vx-input-group prependClasses="border border-solid border-grey border-r-0">
                    <template slot="prepend">
                        <div class="prepend-text">
                            <vs-checkbox v-model="paymentMethods['paymentMethodOnline'].enabled"></vs-checkbox>
                        </div>
                    </template>
                    <vs-input
                        class="w-full"
                        icon-pack="feather" icon="icon-dollar-sign" icon-no-border
                        v-model="paymentMethods['paymentMethodOnline'].fee"
                        placeholder="Fee"
                        type="number"
                        min="0"
                        step="0.1"
                    />
                </vx-input-group>
            </div>
            
            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="saveSettings" color="primary" type="filled" :disabled="!isValidForm('checkout_settings')">Save settings</vs-button>
            </div>
        </vs-popup>

    </vx-card>
</template>

<script>
import { VueContext } from 'vue-context'

export default {
  components: {
    VueContext,
  },
  data () {
    return {
      mode: 'view',

      objects: [],
      selected: [],
      deleted: [],

      maxItems: 20,

      myId: 0,

      editingObject: -1,

      showPopup: {
        'create_qty': false,
        'edit_qty': false,
        'checkout_settings': false,
      },
      newObject: {
        'create': {
          name: '',
          tag: '',
          advertiser: '',
          country: '',
          info: {
            'image': '',
            'description': '',
            'sku': '',
            'cost': 0.0,
            'stock_quantity': 0,
            'size': '',
            'vat': 22.0,
            'url': '',
          },
        },
        'edit': {
          name: '',
          tag: '',
          advertiser: '',
          country: '',
          info: {
            'image': '',
            'description': '',
            'sku': '',
            'cost': 0.0,
            'stock_quantity': 0,
            'size': '',
            'vat': 22.0,
            'url': '',
          },
        },
        'create_qty': {
          quantity: '',
          title: '',
          price: '',
          commission: '',
          sub_commission: '',
          old_price: '',
          discount: '',
        },
        'edit_qty': {
          quantity: '',
          title: '',
          price: '',
          commission: '',
          sub_commission: '',
          old_price: '',
          discount: '',
        },
      },
      quantityOptions: [
          {
            'quantity': '1',
            'price': 99.99,
            'commission': 99.99,
            'sub_commission': 99.99,
            'default': true,
          },
      ],
      quantityDefaultIndex: 0,

      tags: [],
      advertisers: [],

      fileToUpload: null,

      createProductImagePreview: '',
      editProductImagePreview: '',

      plugins: {
        'privacy_policy': {
          enabled: false,
          url: '',
        },
        'upsell': {
          enabled: false,
          best_seller: '',
        },
        'facebook': {
          enabled: false,
          pixel: '',
          access_token: '',
          test_code: '',
          eventValue: '',
          eventCurrency: 'EUR',
        },
        'tiktok': {
          enabled: false,
          pixel: '',
          token: '',
          eventName: 'SubmitForm',
        },
      },

      paymentMethods: {
        'paymentMethodCod': {
          enabled: false,
          fee: '0',
          name: 'Alla consegna',
        },
        'paymentMethodOnline': {
          enabled: false,
          fee: '0',
          name: 'Online',
        },
      },

      upSellOptions: [],
      facebookEventCurrencyOptions: ['EUR', 'USD'],
      tiktokEventNameOptions: ['SubmitForm', 'ClickButton'],
    }
  },
  computed: {
    paginationPageSize () {
      if (this.gridApi) return this.gridApi.paginationGetPageSize()
      else return 50
    },
    totalPages () {
      if (this.gridApi) return this.gridApi.paginationGetTotalPages()
      else return 0
    },
    currentPage: {
      get () {
        if (this.gridApi) return this.gridApi.paginationGetCurrentPage() + 1
        else return 1
      },
      set (val) {
        this.gridApi.paginationGoToPage(val - 1)
      }
    },
    canCreateAndEdit() {
      return (this.$store.getters.userHasRole('developer') || this.$store.getters.userHasRole('admin') ||
              this.$store.getters.userHasRole('advertiser') || this.$store.getters.userHasRole('mediabuyer'))
    },
    isAdmin() {
      return (this.$store.getters.userHasRole('developer') || this.$store.getters.userHasRole('admin'))
    },
  },
  methods: {
    getObjectIndexById(id) {
      var index = -1;
      this.objects.forEach((obj, i) => {
        if (obj.id == id) {
          index = i
          return
        }
      })
      return index
    },
    async retrieveObjects() {
      this.selected = []
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      // Retrieve products
      const response = await this.$http.get('/api/v1/products', { headers })
      this.objects = response.data.data
      for (let i = 0; i < this.objects.length; i++) {
        this.objects[i].advertiser_name = this.objects[i].advertiser.name

        let sku = ''
        let cost = 0.0
        let price = 0.0
        let stock_quantity = 0
        let url = ''

        if (this.objects[i].info != null) {
          if (this.objects[i].info.hasOwnProperty('sku')) {
            sku = this.objects[i].info.sku
          }
          if (this.objects[i].info.hasOwnProperty('cost')) {
            cost = parseFloat(this.objects[i].info.cost)
          }
          if (this.objects[i].info.hasOwnProperty('stock_quantity')) {
            stock_quantity = parseInt(this.objects[i].info.stock_quantity)
          }
          if (this.objects[i].info.hasOwnProperty('url')) {
            url = this.objects[i].info.url
          }
        }

        if (this.objects[i].quantity != null) {
          if (this.objects[i].quantity.hasOwnProperty('options') &&
              this.objects[i].quantity.options.hasOwnProperty('1')) {
            price = parseFloat(this.objects[i].quantity.options['1'].price)
          }
        }

        this.objects[i].sku = sku
        this.objects[i].cost = cost
        this.objects[i].price = price
        this.objects[i].stock_quantity = stock_quantity
        this.objects[i].url = url
      }
      this.$vs.loading.close()
    },
    onPageChanged(val) {
      this.currentPage = val
    },
    confirmDeletePopup() {
      if (this.selected.length == 0) return
      this.$vs.dialog({
        type: 'confirm',
        color: 'danger',
        title: 'Delete confirmation',
        text: 'Are you sure you want to delete ' + this.selected.length + ' object(s)?',
        accept: this.deleteSelected
      })
    },
    deleteSelected() {
      if (this.selected.length == 0) return

      this.$vs.loading()

      this.deleted = []

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      this.selected.forEach((product) => {
        this.$http.delete('/api/v1/products/' + product.id, { headers })
          .then((response) => {
            this.deleted.push(product.id)
            if (this.deleted.length == this.selected.length) {
              this.$vs.loading.close()
              this.selected = []
              this.$vs.notify({
                title: 'Success',
                text: this.deleted.length + ' Product(s) deleted',
                iconPack: 'feather',
                icon: 'icon-alert-circle',
                color: 'success'
              })
              this.selected = []
              this.deleted = []
              this.retrieveObjects()
            }
          })
          .catch((error) => {
            this.$vs.loading.close()
            this.$vs.notify({
              title: 'Error',
              text: error.message,
              iconPack: 'feather',
              icon: 'icon-alert-circle',
              color: 'danger'
            })
          })
      })
    },
    showForm(type) {
      this.showPopup[type] = true
    },
    clearForm(type) {
      if (type == 'create_qty' || type == 'edit_qty') {
        this.newObject[type] = {
          quantity: '',
          title: '',
          price: '',
          commission: '',
          sub_commission: '',
          old_price: '',
          discount: '',
        }
      } else {
        this.newObject[type] = {
          name: '',
          tag: '',
          advertiser: '',
          country: '',
          info: {
            'image': '',
            'description': '',
            'sku': '',
            'cost': 0.0,
            'stock_quantity': 0,
            'size': '',
            'vat': 22.0,
            'url': '',
          },
        }
      }
    },
    isValidForm(type) {
      let obj = this.newObject[type]
      if (type == 'create_qty' || type == 'edit_qty') {
        return (obj.quantity != '' && obj.title != ''
          && obj.price != '' && obj.price >= 0
          && obj.commission != '' && obj.commission >= 0
          && obj.sub_commission != '' && obj.sub_commission >= 0)
      } else if (type == 'checkout_settings') {
        return true
      } else {
        return (obj.name.length > 0
          && obj.tag != '' && obj.tag >= 1
          && obj.advertiser != '' && obj.advertiser >= 1)
      }
    },
    createObject() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let data = new FormData()
      data.append('name', this.newObject['create'].name)
      data.append('tag_id', this.newObject['create'].tag)
      data.append('advertiser_id', this.newObject['create'].advertiser)
      data.append('country', this.newObject['create'].country)
      data.append('info', JSON.stringify(this.newObject['create'].info))

      let quantity = {
        options: {},
        default: '',
      }
      this.quantityOptions.forEach((opt) => {
        quantity['options'][opt.quantity] = {
          title: opt.title,
          price: opt.price,
          commission: opt.commission,
          sub_commission: opt.sub_commission,
          old_price: opt.old_price,
          discount: opt.discount,
        }
      })
      quantity['default'] = this.quantityOptions[this.quantityDefaultIndex].quantity
      quantity = JSON.stringify(quantity)
      data.append('quantity', quantity)

      if (this.fileToUpload != null) {
        data.append('image', this.fileToUpload)
      }

      // Create product
      this.$http.post('/api/v1/products', data, { headers: headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Product created',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
          this.clearForm('create')
          this.clearForm('create_qty')
          this.mode = 'view'
          this.retrieveObjects()
        })
        .catch((error) => {
          this.$vs.loading.close()
          for (const [key, value] of Object.entries(error.response.data.errors)) {
            for (let i = 0; i < value.length; i++) {
              this.errors.add({
                scope: 'create',
                field: key,
                msg: value[i]
              })
            }
          }
        })
    },
    editForm(id) {
      let objIndex = this.getObjectIndexById(id)
      let obj = this.objects[objIndex]

      let info = {
        'image': '',
        'description': '',
        'sku': '',
        'cost': 0.0,
        'stock_quantity': 0,
        'size': '',
        'vat': 22.0,
        'url': '',
      }
      if (obj.info != null) {
        for (const [key, value] of Object.entries(info)) {
          if (obj.info.hasOwnProperty(key)) {
            info[key] = obj.info[key]
          }
        }
      }

      this.newObject['edit'] = {
        name: obj.name,
        tag: obj.tag.id,
        advertiser: obj.advertiser.id,
        country: obj.country,
        info: info,
        image: obj.image,
      }

      // Fetch quantity options
      this.quantityOptions = []
      let index = 0
      for (const [key, value] of Object.entries(obj.quantity.options)) {
        let d = (obj.quantity.default == key)
        this.quantityOptions.push({
          quantity: key,
          title: value.title,
          price: value.price,
          commission: value.commission,
          sub_commission: value.sub_commission,
          old_price: value.old_price,
          discount: value.discount,
          default: d,
        });
        if (d) {
          this.quantityDefaultIndex = index
        }
        index++
      }

      this.fileToUpload = null

      if (obj.hasOwnProperty('image') && obj.image != null) {
        this.editProductImagePreview = obj.image
      }

      this.editingObject = obj.id
      this.mode = 'edit'
    },
    updateObject() {
      if (this.editingObject <= 0) return

      let id = this.editingObject

      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let data = new FormData()
      data.append('name', this.newObject['edit'].name)
      data.append('tag_id', this.newObject['edit'].tag)
      data.append('advertiser_id', this.newObject['edit'].advertiser)
      data.append('country', this.newObject['edit'].country)
      data.append('info', JSON.stringify(this.newObject['edit'].info))

      let quantity = {
        options: {},
        default: '',
      }
      this.quantityOptions.forEach((opt) => {
        quantity['options'][opt.quantity] = {
          title: opt.title,
          price: opt.price,
          commission: opt.commission,
          sub_commission: opt.sub_commission,
          old_price: opt.old_price,
          discount: opt.discount,
        }
      })
      quantity['default'] = this.quantityOptions[this.quantityDefaultIndex].quantity
      quantity = JSON.stringify(quantity)
      data.append('quantity', quantity)

      if (this.fileToUpload != null) {
        data.append('image', this.fileToUpload)
      }

      // Update product
      this.$http.post('/api/v1/products/' + id, data, { headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Product updated',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
          this.clearForm('edit')
          this.clearForm('edit_qty')
          this.mode = 'view'
          this.retrieveObjects()
        })
        .catch((error) => {
          this.$vs.loading.close()
          for (const [key, value] of Object.entries(error.response.data.errors)) {
            for (let i = 0; i < value.length; i++) {
              this.errors.add({
                scope: 'edit',
                field: key,
                msg: value[i]
              })
            }
          }
        })
      this.editingObject = -1
    },
    deleteObject(id) {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      this.$http.delete('/api/v1/products/' + id, { headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Product deleted',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
          this.retrieveObjects()
        })
        .catch((error) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Error',
            text: error.message,
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'danger'
          })
        })
    },
    beginCreation() {
      this.quantityOptions = [
          {
            'quantity': '1',
            'price': 99.99,
            'commission': 99.99,
            'sub_commission': 99.99,
            'default': true,
            'old_price': 0,
            'discount': 0,
          },
      ]
      this.fileToUpload = null
      this.quantityDefaultIndex = 0
      this.mode = 'create'
    },
    changeQuantityDefault() {
      for (let i = 0; i < this.quantityOptions.length; i++) {
        if (i == this.quantityDefaultIndex) {
          this.quantityOptions[i].default = true
        } else {
          this.quantityOptions[i].default = false
        }
      }
    },
    showEditQuantityOptionForm(index) {
      let obj = this.quantityOptions[index]
      this.newObject['edit_qty'] = {
        quantity: obj.quantity,
        price: obj.price,
        commission: obj.commission,
        sub_commission: obj.sub_commission,
        old_price: obj.old_price,
        discount: obj.discount,
      }
      this.editingQuantityOptionIndex = index
      this.showForm('edit_qty')
    },
    editQuantityOption() {
      this.quantityOptions[this.editingQuantityOptionIndex] = this.newObject['edit_qty']
      this.clearForm('edit_qty')
      this.editingQuantityOptionIndex = -1
      this.showPopup['edit_qty'] = false
    },
    removeQuantityOption(index) {
      if (index >= this.quantityOptions.length) return

      let defaultRemoved = this.quantityOptions[index].default
      this.quantityOptions = this.quantityOptions.splice(index, 1)
      if (defaultRemoved && this.quantityOptions.length > 0) {
        this.quantityOptions[0].default = true
      }
    },
    createQuantityOption() {
      this.quantityOptions.push({
        quantity: this.newObject['create_qty'].quantity,
        title: this.newObject['create_qty'].title,
        price: this.newObject['create_qty'].price,
        commission: this.newObject['create_qty'].commission,
        sub_commission: this.newObject['create_qty'].sub_commission,
        old_price: this.newObject['create_qty'].old_price,
        discount: this.newObject['create_qty'].discount,
      })
      this.showPopup['create_qty'] = false
      this.clearForm('create_qty')
    },
    showFilePicker() {
      this.$refs.fileUpload.click()
    },
    onFilesUploadChange(event) {
      this.fileToUpload = event.target.files[0]

      if (this.mode == 'create') {
        this.createProductImagePreview = URL.createObjectURL(this.fileToUpload)
      } else if (this.mode == 'edit') {
        this.editProductImagePreview = URL.createObjectURL(this.fileToUpload)
      }
    },
    async copyToClipboard(toCopy, notificationMessage = 'Copied to the clipboard') {
      await await window.navigator.clipboard.writeText(toCopy)
      this.$vs.notify({
        title: 'Success',
        text: notificationMessage,
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
    },
    async copyCheckoutLink(productId) {
      let url = new URL(window.location)
      let toCopy = url.origin + '/products/' + productId + '/checkout'
      await this.copyToClipboard(toCopy, 'Checkout link copied')
    },
    resetPlugins() {
      this.plugins = {
        'privacy_policy': {
          enabled: false,
          url: '',
        },
        'upsell': {
          enabled: false,
          best_seller: '',
        },
        'facebook': {
          enabled: false,
          pixel: '',
          access_token: '',
          test_code: '',
          eventValue: '',
          eventCurrency: 'EUR',
        },
        'tiktok': {
          enabled: false,
          pixel: '',
          token: '',
          eventName: 'SubmitForm',
        },
      }
    },
    resetPaymentMethods() {
      this.paymentMethods = {
        'paymentMethodCod': {
          enabled: false,
          fee: '0',
          name: 'Alla consegna',
        },
        'paymentMethodOnline': {
          enabled: false,
          fee: '0',
          name: 'Online',
        },
      }
    },
    showSettings(product = null) {
      if (product === null) {
        let index = this.getObjectIndexById(this.editingObject)
        if (index >= 0) {
          product = this.objects[index]
        }
      }

      if (product === null) {
        return
      }

      let quantity = product.quantity
      if (quantity === null) {
        quantity = {
          default: null,
          options: [],
        }
      }
      this.upSellOptions = []
      for (let q in quantity.options) {
        this.upSellOptions.push(q)
      }

      let info = product.info
      if (info === null) {
        info = {}
      }

      if (info.hasOwnProperty('checkout') && info.checkout !== null) {
        let checkout = info.checkout

        this.resetPlugins()
        if (checkout.hasOwnProperty('plugins') && checkout.plugins !== null) {
          let plugins = checkout.plugins
          for (let k in this.plugins) {
            if (plugins.hasOwnProperty(k)) {
              this.plugins[k] = plugins[k]
            }
          }
        }

        this.resetPaymentMethods()
        if (checkout.hasOwnProperty('payment_methods') && checkout.payment_methods !== null) {
          let paymentMethods = checkout.payment_methods
          for (let k in this.paymentMethods) {
            if (paymentMethods.hasOwnProperty(k)) {
              this.paymentMethods[k] = paymentMethods[k]
            }
          }
        }
      }

      this.showPopup['checkout_settings'] = true
    },
    async saveSettings() {
      let product = null
      let index = this.getObjectIndexById(this.editingObject)
      if (index >= 0) {
        product = this.objects[index]
      } else {
        return
      }

      this.$vs.loading()

      let info = product.info
      if (info === null) {
        info = {}
      }

      info.checkout = {
        plugins: this.plugins,
        payment_methods: this.paymentMethods,
      }
      let params = {
        info: info,
      }
      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      // Update product
      let updated = false
      try {
        let data = new FormData()
        data.append('info', JSON.stringify(info))
        const response = await this.$http.post('/api/v1/products/' + this.editingObject, data, { headers })
        updated = true
      } catch (error) {
        updated = false
      }

      if (updated) {
        this.$vs.notify({
          title: 'Success',
          text: 'Product updated',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        })
      } else {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot update the product',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      }

      this.showPopup['checkout_settings'] = false
      this.$vs.loading.close()
    },
  },
  created() {
    this.retrieveObjects()

    let headers = {
      Authorization: 'Bearer ' + localStorage.getItem('accessToken')
    }

    // Retrieve tags
    this.$vs.loading()
    this.$http.get('/api/v1/tags/', { headers })
      .then((response) => {
        this.tags = []
        response.data.data.forEach((tag) => {
          this.tags.push({
            id: tag.id,
            label: tag.name,
          })
        })
        this.$vs.loading.close()
      })
      .catch((error) => {
        this.$vs.loading.close()
      })

    // Retrieve advertisers
    let data = {}
    data['filter_roles'] = [5],
    this.$vs.loading()
    this.$http.get('/api/v1/users/', { headers, params: data })
      .then((response) => {
        this.advertisers = []
        response.data.data.forEach((advertiser) => {
          this.advertisers.push({
            id: advertiser.id,
            label: advertiser.name,
          })
        })
        this.$vs.loading.close()
      })
      .catch((error) => {
        this.$vs.loading.close()
      })
  },
}

</script>

<style scoped>
.column-no-padding {
  padding-top: 0px !important;
  padding-bottom: 0px !important;
  white-space: nowrap;
}
</style>
