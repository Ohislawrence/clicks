<template>
    <AppLayout title="Blacklist Management">
        <template #header>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Blacklist Management</h2>
                <p class="mt-1 text-sm text-gray-600">Manage traffic filtering and blocking rules</p>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total Entries</p>
                                <p class="text-2xl font-bold text-gray-900">{{ stats.total || 0 }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Active Rules</p>
                                <p class="text-2xl font-bold text-gray-900">{{ stats.active || 0 }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total Blocks</p>
                                <p class="text-2xl font-bold text-gray-900">{{ formatNumber(stats.total_hits || 0) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Critical Rules</p>
                                <p class="text-2xl font-bold text-gray-900">{{ stats.by_severity?.critical || 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Bar -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex flex-wrap gap-2">
                            <button
                                @click="openCreateModal"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Add Entry
                            </button>

                            <button
                                @click="openImportModal"
                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                Import CSV
                            </button>

                            <a
                                :href="exportUrl"
                                class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                                </svg>
                                Export CSV
                            </a>

                            <button
                                @click="openTestModal"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                Test Blacklist
                            </button>
                        </div>

                        <div v-if="selectedItems.length > 0" class="flex items-center gap-2">
                            <span class="text-sm text-gray-600">{{ selectedItems.length }} selected</span>
                            <button
                                @click="confirmBulkDelete"
                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Delete Selected
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <input
                                v-model="searchForm.search"
                                type="text"
                                placeholder="Search by value or reason..."
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                @input="debouncedSearch"
                            />
                        </div>
                        
                        <select 
                            v-model="searchForm.type"
                            @change="applyFilters"
                            class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">All Types</option>
                            <option value="ip">IP Address</option>
                            <option value="ip_range">IP Range</option>
                            <option value="user_agent">User Agent</option>
                            <option value="referrer">Referrer</option>
                            <option value="device_fingerprint">Device Fingerprint</option>
                            <option value="country">Country</option>
                            <option value="asn">ASN</option>
                        </select>

                        <select 
                            v-model="searchForm.scope"
                            @change="applyFilters"
                            class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">All Scopes</option>
                            <option value="global">Global</option>
                            <option value="offer">Offer Specific</option>
                            <option value="affiliate">Affiliate Specific</option>
                        </select>

                        <select 
                            v-model="searchForm.severity"
                            @change="applyFilters"
                            class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">All Severities</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="critical">Critical</option>
                        </select>

                        <select 
                            v-model="searchForm.is_active"
                            @change="applyFilters"
                            class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">All Statuses</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>

                <!-- Blacklist Table -->
                <div v-if="blacklists.data.length > 0" class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">
                                        <input
                                            type="checkbox"
                                            @change="toggleSelectAll"
                                            :checked="allSelected"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        />
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Type
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Value
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Scope
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Severity
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Action
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Hits
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="blacklist in blacklists.data" :key="blacklist.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <input
                                            type="checkbox"
                                            :value="blacklist.id"
                                            v-model="selectedItems"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span 
                                            class="px-3 py-1 rounded-full text-xs font-semibold"
                                            :class="getTypeColor(blacklist.type)"
                                        >
                                            {{ formatType(blacklist.type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900 max-w-xs truncate" :title="blacklist.value">
                                            {{ blacklist.value }}
                                        </div>
                                        <div v-if="blacklist.reason" class="text-xs text-gray-500 max-w-xs truncate" :title="blacklist.reason">
                                            {{ blacklist.reason }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-900">{{ formatScope(blacklist.scope) }}</span>
                                        <div v-if="blacklist.scope === 'offer' && blacklist.offer" class="text-xs text-gray-500">
                                            Offer #{{ blacklist.offer_id }}
                                        </div>
                                        <div v-if="blacklist.scope === 'affiliate' && blacklist.scope_entity" class="text-xs text-gray-500">
                                            {{ blacklist.scope_entity.name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span 
                                            class="px-3 py-1 rounded-full text-xs font-semibold"
                                            :class="getSeverityColor(blacklist.severity)"
                                        >
                                            {{ blacklist.severity.toUpperCase() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="text-sm text-gray-900">{{ formatAction(blacklist.action) }}</span>
                                            <span v-if="blacklist.action === 'reduce_quality'" class="ml-2 text-xs text-red-600">
                                                -{{ blacklist.quality_penalty }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ formatNumber(blacklist.hit_count || 0) }}</div>
                                        <div v-if="blacklist.last_hit_at" class="text-xs text-gray-500">
                                            {{ formatDateTime(blacklist.last_hit_at) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col gap-1">
                                            <span 
                                                class="px-3 py-1 rounded-full text-xs font-semibold text-center"
                                                :class="{
                                                    'bg-green-100 text-green-800': blacklist.is_active,
                                                    'bg-gray-100 text-gray-800': !blacklist.is_active
                                                }"
                                            >
                                                {{ blacklist.is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                            <span 
                                                v-if="blacklist.expires_at"
                                                class="text-xs text-gray-500"
                                                :title="`Expires: ${formatDateTime(blacklist.expires_at)}`"
                                            >
                                                Expires {{ formatRelativeTime(blacklist.expires_at) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <button
                                                @click="openEditModal(blacklist)"
                                                class="text-blue-600 hover:text-blue-900"
                                                title="Edit"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            
                                            <button
                                                @click="toggleActive(blacklist)"
                                                :class="blacklist.is_active ? 'text-yellow-600 hover:text-yellow-900' : 'text-green-600 hover:text-green-900'"
                                                :title="blacklist.is_active ? 'Deactivate' : 'Activate'"
                                            >
                                                <svg v-if="blacklist.is_active" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </button>

                                            <button
                                                @click="confirmDelete(blacklist)"
                                                class="text-red-600 hover:text-red-900"
                                                title="Delete"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="bg-white px-6 py-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Showing {{ blacklists.from || 0 }} to {{ blacklists.to || 0 }} of {{ blacklists.total || 0 }} entries
                            </div>
                            <div class="flex space-x-2">
                                <component
                                    v-for="(link, index) in blacklists.links"
                                    :key="index"
                                    :is="link.url ? Link : 'span'"
                                    :href="link.url || undefined"
                                    :class="[
                                        'px-3 py-2 rounded-lg text-sm transition-colors',
                                        link.active 
                                            ? 'bg-blue-600 text-white' 
                                            : link.url 
                                                ? 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' 
                                                : 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                    ]"
                                    v-html="link.label"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No blacklist entries found</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new blacklist entry</p>
                    <button
                        @click="openCreateModal"
                        class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add First Entry
                    </button>
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <DialogModal :show="showFormModal" @close="closeFormModal" max-width="2xl">
            <template #title>
                {{ editingBlacklist ? 'Edit Blacklist Entry' : 'Create Blacklist Entry' }}
            </template>

            <template #content>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                            <select
                                v-model="form.type"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="">Select type...</option>
                                <option value="ip">IP Address</option>
                                <option value="ip_range">IP Range (CIDR)</option>
                                <option value="user_agent">User Agent</option>
                                <option value="referrer">Referrer</option>
                                <option value="device_fingerprint">Device Fingerprint</option>
                                <option value="country">Country (ISO Code)</option>
                                <option value="asn">ASN</option>
                            </select>
                            <div v-if="form.errors.type" class="text-red-600 text-sm mt-1">{{ form.errors.type }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Match Type *</label>
                            <select
                                v-model="form.match_type"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="exact">Exact Match</option>
                                <option value="contains">Contains</option>
                                <option value="regex">Regex</option>
                                <option value="wildcard">Wildcard</option>
                            </select>
                            <div v-if="form.errors.match_type" class="text-red-600 text-sm mt-1">{{ form.errors.match_type }}</div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Value *</label>
                        <input
                            v-model="form.value"
                            type="text"
                            placeholder="e.g., 192.168.1.100, 10.0.0.0/8, bot, spam-site.com, NG"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        />
                        <div v-if="form.errors.value" class="text-red-600 text-sm mt-1">{{ form.errors.value }}</div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Scope *</label>
                            <select
                                v-model="form.scope"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="global">Global</option>
                                <option value="offer">Specific Offer</option>
                                <option value="affiliate">Specific Affiliate</option>
                            </select>
                            <div v-if="form.errors.scope" class="text-red-600 text-sm mt-1">{{ form.errors.scope }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Severity *</label>
                            <select
                                v-model="form.severity"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                            <div v-if="form.errors.severity" class="text-red-600 text-sm mt-1">{{ form.errors.severity }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Action *</label>
                            <select
                                v-model="form.action"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="block">Block</option>
                                <option value="reduce_quality">Reduce Quality</option>
                                <option value="flag">Flag Only</option>
                            </select>
                            <div v-if="form.errors.action" class="text-red-600 text-sm mt-1">{{ form.errors.action }}</div>
                        </div>
                    </div>

                    <div v-if="form.action === 'reduce_quality'">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Quality Penalty (0-100) *
                        </label>
                        <input
                            v-model.number="form.quality_penalty"
                            type="number"
                            min="0"
                            max="100"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        />
                        <div v-if="form.errors.quality_penalty" class="text-red-600 text-sm mt-1">{{ form.errors.quality_penalty }}</div>
                    </div>

                    <div v-if="form.scope === 'offer'">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Offer ID *</label>
                        <input
                            v-model.number="form.offer_id"
                            type="number"
                            min="1"
                            placeholder="Enter offer ID"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        />
                        <div v-if="form.errors.offer_id" class="text-red-600 text-sm mt-1">{{ form.errors.offer_id }}</div>
                    </div>

                    <div v-if="form.scope === 'affiliate'">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Affiliate ID *</label>
                        <input
                            v-model.number="form.scope_id"
                            type="number"
                            min="1"
                            placeholder="Enter affiliate user ID"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        />
                        <div v-if="form.errors.scope_id" class="text-red-600 text-sm mt-1">{{ form.errors.scope_id }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reason *</label>
                        <input
                            v-model="form.reason"
                            type="text"
                            placeholder="Why is this being blacklisted?"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        />
                        <div v-if="form.errors.reason" class="text-red-600 text-sm mt-1">{{ form.errors.reason }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                        <textarea
                            v-model="form.notes"
                            rows="3"
                            placeholder="Additional notes or context..."
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        ></textarea>
                        <div v-if="form.errors.notes" class="text-red-600 text-sm mt-1">{{ form.errors.notes }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Expires At (Optional)</label>
                        <input
                            v-model="form.expires_at"
                            type="datetime-local"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        />
                        <p class="text-xs text-gray-500 mt-1">Leave empty for permanent blacklist</p>
                        <div v-if="form.errors.expires_at" class="text-red-600 text-sm mt-1">{{ form.errors.expires_at }}</div>
                    </div>

                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            v-model="form.is_active"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        />
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </div>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="closeFormModal">
                    Cancel
                </SecondaryButton>

                <PrimaryButton
                    class="ml-3"
                    @click="saveBlacklist"
                    :disabled="form.processing"
                >
                    {{ editingBlacklist ? 'Update' : 'Create' }}
                </PrimaryButton>
            </template>
        </DialogModal>

        <!-- Import Modal -->
        <DialogModal :show="showImportModal" @close="closeImportModal">
            <template #title>
                Import Blacklist Entries
            </template>

            <template #content>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            CSV File *
                        </label>
                        <input
                            type="file"
                            accept=".csv,.txt"
                            @change="handleFileUpload"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                        />
                        <div v-if="importForm.errors.file" class="text-red-600 text-sm mt-1">{{ importForm.errors.file }}</div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-900 mb-2">CSV Format:</h4>
                        <pre class="text-xs text-gray-700 overflow-x-auto">type,value,match_type,severity,action,reason,scope,quality_penalty
ip,192.168.1.100,exact,high,block,Spam IP,global,100
user_agent,bot,contains,medium,flag,Bot traffic,global,0</pre>
                    </div>

                    <div v-if="importResult" class="rounded-lg p-4" :class="importResult.success ? 'bg-green-50' : 'bg-red-50'">
                        <p class="text-sm font-medium" :class="importResult.success ? 'text-green-800' : 'text-red-800'">
                            {{ importResult.message }}
                        </p>
                        <div v-if="importResult.errors && importResult.errors.length > 0" class="mt-2">
                            <p class="text-xs font-semibold text-red-700 mb-1">Errors:</p>
                            <ul class="text-xs text-red-600 space-y-1">
                                <li v-for="(error, index) in importResult.errors.slice(0, 5)" :key="index">{{ error }}</li>
                                <li v-if="importResult.errors.length > 5" class="text-red-500 italic">
                                    ...and {{ importResult.errors.length - 5 }} more errors
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="closeImportModal">
                    Close
                </SecondaryButton>

                <PrimaryButton
                    v-if="!importResult || !importResult.success"
                    class="ml-3"
                    @click="importBlacklist"
                    :disabled="importForm.processing || !importForm.file"
                >
                    Import
                </PrimaryButton>
            </template>
        </DialogModal>

        <!-- Test Modal -->
        <DialogModal :show="showTestModal" @close="closeTestModal">
            <template #title>
                Test Blacklist
            </template>

            <template #content>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <select
                                v-model="testForm.type"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="ip">IP Address</option>
                                <option value="user_agent">User Agent</option>
                                <option value="referrer">Referrer</option>
                                <option value="country">Country</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Value</label>
                            <input
                                v-model="testForm.value"
                                type="text"
                                placeholder="Enter value to test"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            />
                        </div>
                    </div>

                    <button
                        @click="runTest"
                        :disabled="testForm.processing || !testForm.value"
                        class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Test Now
                    </button>

                    <div v-if="testResult" class="rounded-lg p-4" :class="testResult.is_blacklisted ? 'bg-red-50 border-2 border-red-200' : 'bg-green-50 border-2 border-green-200'">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg v-if="testResult.is_blacklisted" class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <svg v-else class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-semibold" :class="testResult.is_blacklisted ? 'text-red-800' : 'text-green-800'">
                                    {{ testResult.is_blacklisted ? '⛔ BLACKLISTED' : '✓ NOT BLACKLISTED' }}
                                </h3>
                                <div v-if="testResult.is_blacklisted" class="mt-2 text-sm text-red-700">
                                    <p><strong>Matched Rule:</strong> {{ testResult.matched_value }}</p>
                                    <p><strong>Match Type:</strong> {{ testResult.match_type }}</p>
                                    <p><strong>Severity:</strong> {{ testResult.severity?.toUpperCase() }}</p>
                                    <p><strong>Action:</strong> {{ formatAction(testResult.action) }}</p>
                                    <p v-if="testResult.quality_penalty"><strong>Quality Penalty:</strong> -{{ testResult.quality_penalty }} points</p>
                                    <p><strong>Reason:</strong> {{ testResult.reason }}</p>
                                </div>
                                <p v-else class="mt-1 text-sm text-green-700">
                                    This value is not blacklisted and will be allowed through.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="closeTestModal">
                    Close
                </SecondaryButton>
            </template>
        </DialogModal>

        <!-- Delete Confirmation Modal -->
        <DialogModal :show="showDeleteModal" @close="closeDeleteModal">
            <template #title>
                Delete Blacklist Entry
            </template>

            <template #content>
                <p>Are you sure you want to delete this blacklist entry?</p>
                <div v-if="itemToDelete" class="mt-3 p-3 bg-gray-50 rounded-lg">
                    <p class="text-sm"><strong>Type:</strong> {{ formatType(itemToDelete.type) }}</p>
                    <p class="text-sm"><strong>Value:</strong> {{ itemToDelete.value }}</p>
                    <p class="text-sm"><strong>Reason:</strong> {{ itemToDelete.reason }}</p>
                </div>
                <p class="mt-3 text-sm text-gray-600">This action cannot be undone.</p>
            </template>

            <template #footer>
                <SecondaryButton @click="closeDeleteModal">
                    Cancel
                </SecondaryButton>

                <DangerButton
                    class="ml-3"
                    @click="deleteBlacklist"
                    :disabled="deleteForm.processing"
                >
                    Delete
                </DangerButton>
            </template>
        </DialogModal>

        <!-- Bulk Delete Confirmation Modal -->
        <DialogModal :show="showBulkDeleteModal" @close="closeBulkDeleteModal">
            <template #title>
                Delete Multiple Entries
            </template>

            <template #content>
                <p>Are you sure you want to delete <strong>{{ selectedItems.length }}</strong> blacklist entries?</p>
                <p class="mt-3 text-sm text-gray-600">This action cannot be undone.</p>
            </template>

            <template #footer>
                <SecondaryButton @click="closeBulkDeleteModal">
                    Cancel
                </SecondaryButton>

                <DangerButton
                    class="ml-3"
                    @click="bulkDelete"
                    :disabled="bulkDeleteForm.processing"
                >
                    Delete {{ selectedItems.length }} Entries
                </DangerButton>
            </template>
        </DialogModal>
    </AppLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { router, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DialogModal from '@/Components/DialogModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { debounce } from 'lodash';

const props = defineProps({
    blacklists: Object,
    stats: Object,
    filters: Object,
});

// Search and filters
const searchForm = reactive({
    search: props.filters?.search || '',
    type: props.filters?.type || '',
    scope: props.filters?.scope || '',
    severity: props.filters?.severity || '',
    is_active: props.filters?.is_active || '',
});

const applyFilters = () => {
    router.get(route('admin.blacklists.index'), searchForm, {
        preserveState: true,
        preserveScroll: true,
    });
};

const debouncedSearch = debounce(() => {
    applyFilters();
}, 500);

// Export URL with filters
const exportUrl = computed(() => {
    const params = new URLSearchParams();
    Object.keys(searchForm).forEach(key => {
        if (searchForm[key]) {
            params.append(key, searchForm[key]);
        }
    });
    return route('admin.blacklists.export') + '?' + params.toString();
});

// Selection
const selectedItems = ref([]);
const allSelected = computed(() => {
    return props.blacklists.data.length > 0 && selectedItems.value.length === props.blacklists.data.length;
});

const toggleSelectAll = () => {
    if (allSelected.value) {
        selectedItems.value = [];
    } else {
        selectedItems.value = props.blacklists.data.map(item => item.id);
    }
};

// Create/Edit Modal
const showFormModal = ref(false);
const editingBlacklist = ref(null);

const form = useForm({
    type: '',
    value: '',
    match_type: 'exact',
    scope: 'global',
    scope_id: null,
    offer_id: null,
    is_active: true,
    severity: 'medium',
    action: 'block',
    quality_penalty: 100,
    reason: '',
    notes: '',
    expires_at: '',
});

const openCreateModal = () => {
    editingBlacklist.value = null;
    form.reset();
    form.clearErrors();
    showFormModal.value = true;
};

const openEditModal = (blacklist) => {
    editingBlacklist.value = blacklist;
    form.type = blacklist.type;
    form.value = blacklist.value;
    form.match_type = blacklist.match_type;
    form.scope = blacklist.scope;
    form.scope_id = blacklist.scope_id;
    form.offer_id = blacklist.offer_id;
    form.is_active = blacklist.is_active;
    form.severity = blacklist.severity;
    form.action = blacklist.action;
    form.quality_penalty = blacklist.quality_penalty || 100;
    form.reason = blacklist.reason || '';
    form.notes = blacklist.notes || '';
    form.expires_at = blacklist.expires_at ? blacklist.expires_at.slice(0, 16) : '';
    showFormModal.value = true;
};

const closeFormModal = () => {
    showFormModal.value = false;
    editingBlacklist.value = null;
    form.reset();
    form.clearErrors();
};

const saveBlacklist = () => {
    if (editingBlacklist.value) {
        form.put(route('admin.blacklists.update', editingBlacklist.value.id), {
            onSuccess: () => {
                closeFormModal();
            },
        });
    } else {
        form.post(route('admin.blacklists.store'), {
            onSuccess: () => {
                closeFormModal();
            },
        });
    }
};

// Import Modal
const showImportModal = ref(false);
const importResult = ref(null);

const importForm = useForm({
    file: null,
});

const openImportModal = () => {
    importResult.value = null;
    importForm.reset();
    showImportModal.value = true;
};

const closeImportModal = () => {
    showImportModal.value = false;
    importResult.value = null;
    importForm.reset();
};

const handleFileUpload = (event) => {
    importForm.file = event.target.files[0];
};

const importBlacklist = () => {
    importForm.post(route('admin.blacklists.import'), {
        onSuccess: (response) => {
            importResult.value = response.props.flash || { success: true, message: 'Import completed successfully' };
            importForm.reset();
        },
        onError: (errors) => {
            importResult.value = { success: false, message: 'Import failed', errors: Object.values(errors) };
        },
    });
};

// Test Modal
const showTestModal = ref(false);
const testResult = ref(null);

const testForm = useForm({
    type: 'ip',
    value: '',
});

const openTestModal = () => {
    testResult.value = null;
    testForm.reset();
    showTestModal.value = true;
};

const closeTestModal = () => {
    showTestModal.value = false;
    testResult.value = null;
};

const runTest = () => {
    testForm.post(route('admin.blacklists.test'), {
        onSuccess: (response) => {
            testResult.value = response.props.result || null;
        },
        preserveScroll: true,
        preserveState: true,
    });
};

// Delete Modal
const showDeleteModal = ref(false);
const itemToDelete = ref(null);

const deleteForm = useForm({});

const confirmDelete = (blacklist) => {
    itemToDelete.value = blacklist;
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    itemToDelete.value = null;
};

const deleteBlacklist = () => {
    deleteForm.delete(route('admin.blacklists.destroy', itemToDelete.value.id), {
        onSuccess: () => {
            closeDeleteModal();
        },
    });
};

// Bulk Delete
const showBulkDeleteModal = ref(false);
const bulkDeleteForm = useForm({
    ids: [],
});

const confirmBulkDelete = () => {
    showBulkDeleteModal.value = true;
};

const closeBulkDeleteModal = () => {
    showBulkDeleteModal.value = false;
};

const bulkDelete = () => {
    bulkDeleteForm.ids = selectedItems.value;
    bulkDeleteForm.post(route('admin.blacklists.bulk-destroy'), {
        onSuccess: () => {
            selectedItems.value = [];
            closeBulkDeleteModal();
        },
    });
};

// Toggle Active
const toggleActive = (blacklist) => {
    router.patch(route('admin.blacklists.toggle', blacklist.id), {}, {
        preserveScroll: true,
    });
};

// Formatters
const formatType = (type) => {
    const types = {
        ip: 'IP',
        ip_range: 'IP Range',
        user_agent: 'User Agent',
        referrer: 'Referrer',
        device_fingerprint: 'Fingerprint',
        country: 'Country',
        asn: 'ASN',
    };
    return types[type] || type;
};

const formatScope = (scope) => {
    const scopes = {
        global: 'Global',
        offer: 'Offer',
        affiliate: 'Affiliate',
    };
    return scopes[scope] || scope;
};

const formatAction = (action) => {
    const actions = {
        block: 'Block',
        reduce_quality: 'Reduce Quality',
        flag: 'Flag',
    };
    return actions[action] || action;
};

const getTypeColor = (type) => {
    const colors = {
        ip: 'bg-blue-100 text-blue-800',
        ip_range: 'bg-indigo-100 text-indigo-800',
        user_agent: 'bg-purple-100 text-purple-800',
        referrer: 'bg-pink-100 text-pink-800',
        device_fingerprint: 'bg-yellow-100 text-yellow-800',
        country: 'bg-green-100 text-green-800',
        asn: 'bg-red-100 text-red-800',
    };
    return colors[type] || 'bg-gray-100 text-gray-800';
};

const getSeverityColor = (severity) => {
    const colors = {
        low: 'bg-blue-100 text-blue-800',
        medium: 'bg-yellow-100 text-yellow-800',
        high: 'bg-orange-100 text-orange-800',
        critical: 'bg-red-100 text-red-800',
    };
    return colors[severity] || 'bg-gray-100 text-gray-800';
};

const formatNumber = (number) => {
    return new Intl.NumberFormat().format(number);
};

const formatDateTime = (dateTime) => {
    if (!dateTime) return 'N/A';
    return new Date(dateTime).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const formatRelativeTime = (dateTime) => {
    if (!dateTime) return '';
    const date = new Date(dateTime);
    const now = new Date();
    const diff = date - now;
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    
    if (days < 0) return 'expired';
    if (days === 0) return 'today';
    if (days === 1) return 'tomorrow';
    if (days < 7) return `in ${days} days`;
    if (days < 30) return `in ${Math.floor(days / 7)} weeks`;
    return `in ${Math.floor(days / 30)} months`;
};
</script>
