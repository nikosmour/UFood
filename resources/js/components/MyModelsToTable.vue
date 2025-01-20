<template>
    <v-data-table-virtual
        v-model:expanded = "expanded"
        :headers = "tableHeaders"
        :items = "models"
        :show-expand = "relationships.length !== 0"
        hide-default-footer
        :disableSort = "true"
        :expandOnClick = "relationships.length !== 0"
        :hide-default-header = "$vuetify.display.smAndDown"
        :mobile = "null"

        mobile-breakpoint = "md"
        hover
    >
        <template v-if = "caption" v-slot:top>
            <v-toolbar :title = "caption" flat />
        </template>
        <template v-slot:expanded-row = "{item }">
            <tr v-for = "(relationship, index) in relationships" :key = "'relationship-' + index">
                <td :colspan = "tableHeaders.length+1">
                    <my-models-to-table
                        v-if = "typeof relationshipData(item[relationship])[0] === 'object'"
                        :caption = "$t('model_data.'+relationship)"
                        :models = "relationshipData(item[relationship])"
                    />
                    <v-list v-else>
                        <v-list-subheader :title = "relationship" />
                        <v-list-item
                            v-for = "(value, index2) in relationshipData(item[relationship])"
                            :key = "relationship + '-' + index2" :title = "value"
                        />

                    </v-list>
                </td>
            </tr>
        </template>
        <template v-slot:item.created_at = "{item}">
            {{ ( new Date( item.created_at ) ).toLocaleDateString( "en-ca" ) }}
        </template>
        <template v-slot:item.updated_at = "{item}">
            {{ ( new Date( item.updated_at ) ).toLocaleDateString( "en-ca" ) }}
        </template>
        <template v-slot:item.is_permanent = "{item}">
            {{ $t( item.is_permanent
                   ? "address.permanent"
                   : "address.temporary" ) }}
        </template>
        <template v-slot:item.is_active = "{item}">
            {{ $t( "active", item.is_active
                             ? 1
                             : 0 ) }}
        </template>

    </v-data-table-virtual>
</template>

<script>
export default {
	name :  "MyModelsToTable",
	props : {
		models :  {
			type :     Array,
			required : true,
		},
		caption : {
			type :    String,
			default : "",
		},
	},
	data() {
		return {
			expanded : [],
		};
	},
	computed : {
		firstModel() {
			return this.models.length > 0
			       ? this.models[ 0 ]
			       : {};
		},
		attributes() {
			return Object.keys( this.firstModel )
			             .filter( key => typeof this.firstModel[ key ] !== "object" );
		},
		relationships() {
			return Object.keys( this.firstModel )
			             .filter( key => typeof this.firstModel[ key ] === "object" );
		},
		tableHeaders() {
			return this.attributes.map( ( key ) => {
				return {
				title : this.$t( "model_data." + key.toLowerCase() ),
				value : key,
				key :   key,
				}
			} );
		},
	},
	methods :  {
		relationshipData( data ) {
			return Array.isArray( data )
			       ? data
			       : [ data ];
		},
	},
	mounted() {
		console.info( this.tableHeaders, this.attributes, this.$vuetify );
	},
};
</script>
