<template>
  <v-app>
    <v-main>
      <v-container>

        <!-- Page title -->
        <h2 class="text-h4 mb-4">Customer List</h2>
        <div class="d-flex justify-end mb-4">
          <v-btn
            color="primary"
            :loading="bLoading"
            @click="triggerFileInput"
          >
            Import
          </v-btn>

          <!-- File input -->
          <input
            ref="mFileInput"
            type="file"
            accept=".csv"
            hidden
            @change="uploadCSV"
          />
        </div>

        <!-- Customer List Table -->
        <v-data-table-server
          v-model:page="iPage"
          v-model:items-per-page="iItemsPerPage"
          :headers="aTableHeaders"
          :items="aCustomerList"
          :items-length="iTotalItems"
          :items-per-page-options="aItemsPerPageOptions"
          :loading="bLoading"
          @update:options="fetchCustomers"
          class="elevation-1"
        >
          <template v-slot:item.website="{ item }">
            <div class="text-wrap text-break">
              {{ item.website }}
            </div>
          </template>
        </v-data-table-server>
      </v-container>
    </v-main>
  </v-app>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

/**
 * File input.
 *
 * @type {[type]}
 */
const mFileInput = ref(null)

/**
 * Defines the details for the table header.
 *
 * @type {Array}
 */
const aTableHeaders = [
  { title: 'ID', key: 'id' },
  { title: 'First Name', key: 'first_name' },
  { title: 'Last Name', key: 'last_name' },
  { title: 'Email', key: 'email' },
  { title: 'Gender', key: 'gender' },
  { title: 'IP', key: 'ip' },
  { title: 'Company', key: 'company' },
  { title: 'City', key: 'city' },
  { title: 'Title', key: 'title' },
  { title: 'Website', key: 'website' },
]

/**
 * Defines the available options for the items per page.
 *
 * @type {Array}
 */
const aItemsPerPageOptions = [10, 15, 20, 100]

/**
 * Default selected page
 *
 * @type {Integer}
 */
const iPage = ref(1)

/**
 * Default selected items per page option.
 *
 * @type {Integer}
 */
const iItemsPerPage = ref(10)

/**
 * Total number customers.
 *
 * @type {Integer}
 */
const iTotalItems = ref(0)

/**
 * Variable that holds the list of customers to be displayed.
 *
 * @type {Array}
 */
const aCustomerList = ref([])

/**
 * Flag to identify whether the page (or request) is still loading or not.
 *
 * @type {Boolean}
 */
const bLoading = ref(false)

onMounted(() => {
  fetchCustomers(1, 100)
})

/**
 * Sends a request to fetch the list of customers
 *
 * @return { void }
 */
async function fetchCustomers({ page = iPage.value, itemsPerPage = iItemsPerPage.value } = {}) {
  bLoading.value = true

  try {
    const oResponse = await axios.get('/api/customer', {
      params: {
        page,
        per_page: itemsPerPage
      }
    })

    if (oResponse.data === null || oResponse.data === undefined) {
      return
    }

    aCustomerList.value = oResponse.data.data
    iTotalItems.value = oResponse.data.total

    // Sync options' state
    iPage.value = page
    iItemsPerPage.value = itemsPerPage
  } catch (oError) {
    alert('Fetching customers failed.')
    console.error('Error: Fetching customers failed.', oError)
  } finally {
    bLoading.value = false
  }
}


/**
 * [triggerFileInput description]
 * @return {[type]} [description]
 */
function triggerFileInput() {
  mFileInput.value.click()
}

/**
 * Handles logic to get and upload the selected file.
 * 
 * @param {object} event
 * @return {void}
 */
async function uploadCSV(event) {
  const mFile = event.target.files[0]
  if (mFile === false) {
    return
  }

  try {
    bLoading.value = true
    await importFile(mFile)
    await fetchCustomers()
  } catch (oError) {
    console.error(oError)
    alert('Import failed')
  } finally {
    bLoading.value = false
    event.target.value = ''
  }
}

/**
 * Send API request to import CSV file.
 * 
 * @param  {mixed} mFile
 * @return {void}
 */
async function importFile(mFile) {
  const oFormData = new FormData()
  oFormData.append('file', mFile)

  await axios.post('/api/customer/import', oFormData, {
    headers: {
      'Content-Type': 'multipart/form-data'
    }
  })
}
</script>