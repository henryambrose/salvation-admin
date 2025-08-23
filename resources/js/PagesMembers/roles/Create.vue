<template>
  <Head title="Create Role" />
  
  <AppLayout>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="bg-white shadow">
        <div class="px-4 py-5 sm:px-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Create New Role</h1>
              <p class="mt-1 text-sm text-gray-600">
                Define a new role with specific permissions for your team members.
              </p>
            </div>
            <button
              @click="goBack"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
              </svg>
              Back to Roles
            </button>
          </div>
        </div>
      </div>

      <!-- Form -->
      <div class="mt-6">
        <form @submit.prevent="submitForm" class="space-y-6">
          <!-- Basic Information Card -->
          <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-medium text-gray-900">Basic Information</h2>
            </div>
            <div class="px-6 py-4 space-y-4">
              <!-- Role Name -->
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                  Role Name *
                </label>
                <input
                  id="name"
                  v-model="form.name"
                  type="text"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  placeholder="Enter role name (e.g., Content Editor, Data Manager)"
                />
                <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                  {{ form.errors.name }}
                </div>
              </div>

              <!-- Role Description -->
              <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                  Description
                </label>
                <textarea
                  id="description"
                  v-model="form.description"
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  placeholder="Describe what this role can do..."
                ></textarea>
                <div v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                  {{ form.errors.description }}
                </div>
              </div>
            </div>
          </div>

          <!-- Permissions Card -->
          <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <div class="flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900">Permissions</h2>
                <div class="flex items-center space-x-4">
                  <button
                    type="button"
                    @click="selectAllPermissions"
                    class="text-sm text-indigo-600 hover:text-indigo-500"
                  >
                    Select All
                  </button>
                  <button
                    type="button"
                    @click="clearAllPermissions"
                    class="text-sm text-gray-600 hover:text-gray-500"
                  >
                    Clear All
                  </button>
                </div>
              </div>
            </div>
            <div class="px-6 py-4">
              <!-- Permission Groups by Category -->
              <div v-if="groupedPermissions" class="space-y-8">
                <!-- Member App Section -->
                <div class="border-2 border-blue-200 rounded-lg p-6 bg-blue-50">
                  <h2 class="text-2xl font-bold text-blue-800 mb-6 text-center">
                    Member - App
                  </h2>
                  
                  <!-- Core Management -->
                  <div v-if="groupedPermissions['Core Management']" class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                      <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <div class="w-3 h-3 rounded-full bg-blue-500 mr-3"></div>
                        Core Management
                      </h3>
                      <div class="flex items-center space-x-4">
                        <button
                          type="button"
                          @click="selectCategoryPermissions('Core Management')"
                          class="text-sm text-blue-600 hover:text-blue-500 font-medium"
                        >
                          Select All
                        </button>
                        <button
                          type="button"
                          @click="clearCategoryPermissions('Core Management')"
                          class="text-sm text-gray-600 hover:text-gray-500 font-medium"
                        >
                          Clear All
                        </button>
                      </div>
                    </div>
                    <div class="space-y-4">
                      <div
                        v-for="(permissions, module) in groupedPermissions['Core Management']"
                        :key="`Core Management-${module}`"
                        class="border border-gray-100 rounded-lg p-4 bg-gray-50"
                      >
                        <div class="flex items-center justify-between mb-3">
                          <h4 class="text-base font-medium text-gray-800">
                            {{ module }}
                          </h4>
                          <div class="flex items-center space-x-2">
                            <button
                              type="button"
                              @click="selectModulePermissions('Core Management', module)"
                              class="text-xs text-blue-600 hover:text-blue-500 font-medium"
                            >
                              Select All
                            </button>
                            <span class="text-gray-300">|</span>
                            <button
                              type="button"
                              @click="clearModulePermissions('Core Management', module)"
                              class="text-xs text-gray-600 hover:text-gray-500 font-medium"
                            >
                              Clear All
                            </button>
                          </div>
                        </div>
                        <div class="flex flex-wrap gap-2">
                          <label
                            v-for="permission in permissions"
                            :key="permission.id"
                            class="inline-flex items-center cursor-pointer"
                          >
                            <input
                              v-model="form.permissions"
                              :value="permission.id"
                              type="checkbox"
                              class="sr-only"
                            />
                            <span
                              :class="[
                                'inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium transition-colors duration-200',
                                form.permissions.includes(permission.id)
                                  ? 'bg-blue-100 text-blue-800 ring-2 ring-blue-500'
                                  : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'
                              ]"
                            >
                              <svg
                                v-if="form.permissions.includes(permission.id)"
                                class="w-3 h-3 mr-1.5"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                              >
                                <path
                                  fill-rule="evenodd"
                                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                  clip-rule="evenodd"
                                />
                              </svg>
                              {{ formatPermissionName(permission.name) }}
                            </span>
                          </label>
                        </div>
                        <div class="mt-3 text-xs text-gray-500">
                          {{ permissions.length }} permissions selected
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Organizational Structure -->
                  <div v-if="groupedPermissions['Organizational Structure']" class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                      <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <div class="w-3 h-3 rounded-full bg-green-500 mr-3"></div>
                        Organizational Structure
                      </h3>
                      <div class="flex items-center space-x-4">
                        <button
                          type="button"
                          @click="selectCategoryPermissions('Organizational Structure')"
                          class="text-sm text-green-600 hover:text-green-500 font-medium"
                        >
                          Select All
                        </button>
                        <button
                          type="button"
                          @click="clearCategoryPermissions('Organizational Structure')"
                          class="text-sm text-gray-600 hover:text-gray-500 font-medium"
                        >
                          Clear All
                        </button>
                      </div>
                    </div>
                    <div class="space-y-4">
                      <div
                        v-for="(permissions, module) in groupedPermissions['Organizational Structure']"
                        :key="`Organizational Structure-${module}`"
                        class="border border-gray-100 rounded-lg p-4 bg-gray-50"
                      >
                        <div class="flex items-center justify-between mb-3">
                          <h4 class="text-base font-medium text-gray-800">
                            {{ module }}
                          </h4>
                          <div class="flex items-center space-x-2">
                            <button
                              type="button"
                              @click="selectModulePermissions('Organizational Structure', module)"
                              class="text-xs text-green-600 hover:text-green-500 font-medium"
                            >
                              Select All
                            </button>
                            <span class="text-gray-300">|</span>
                            <button
                              type="button"
                              @click="clearModulePermissions('Organizational Structure', module)"
                              class="text-xs text-gray-600 hover:text-gray-500 font-medium"
                            >
                              Clear All
                            </button>
                          </div>
                        </div>
                        <div class="flex flex-wrap gap-2">
                          <label
                            v-for="permission in permissions"
                            :key="permission.id"
                            class="inline-flex items-center cursor-pointer"
                          >
                            <input
                              v-model="form.permissions"
                              :value="permission.id"
                              type="checkbox"
                              class="sr-only"
                            />
                            <span
                              :class="[
                                'inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium transition-colors duration-200',
                                form.permissions.includes(permission.id)
                                  ? 'bg-green-100 text-green-800 ring-2 ring-green-500'
                                  : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'
                              ]"
                            >
                              <svg
                                v-if="form.permissions.includes(permission.id)"
                                class="w-3 h-3 mr-1.5"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                              >
                                <path
                                  fill-rule="evenodd"
                                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                  clip-rule="evenodd"
                                />
                              </svg>
                              {{ formatPermissionName(permission.name) }}
                            </span>
                          </label>
                        </div>
                        <div class="mt-3 text-xs text-gray-500">
                          {{ permissions.length }} permissions selected
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Leadership -->
                  <div v-if="groupedPermissions['Leadership']" class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                      <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <div class="w-3 h-3 rounded-full bg-purple-500 mr-3"></div>
                        Leadership
                      </h3>
                      <div class="flex items-center space-x-4">
                        <button
                          type="button"
                          @click="selectCategoryPermissions('Leadership')"
                          class="text-sm text-purple-600 hover:text-purple-500 font-medium"
                        >
                          Select All
                        </button>
                        <button
                          type="button"
                          @click="clearCategoryPermissions('Leadership')"
                          class="text-sm text-gray-600 hover:text-gray-500 font-medium"
                        >
                          Clear All
                        </button>
                      </div>
                    </div>
                    <div class="space-y-4">
                      <div
                        v-for="(permissions, module) in groupedPermissions['Leadership']"
                        :key="`Leadership-${module}`"
                        class="border border-gray-100 rounded-lg p-4 bg-gray-50"
                      >
                        <div class="flex items-center justify-between mb-3">
                          <h4 class="text-base font-medium text-gray-800">
                            {{ module }}
                          </h4>
                          <div class="flex items-center space-x-2">
                            <button
                              type="button"
                              @click="selectModulePermissions('Leadership', module)"
                              class="text-xs text-purple-600 hover:text-purple-500 font-medium"
                            >
                              Select All
                            </button>
                            <span class="text-gray-300">|</span>
                            <button
                              type="button"
                              @click="clearModulePermissions('Leadership', module)"
                              class="text-xs text-gray-600 hover:text-gray-500 font-medium"
                            >
                              Clear All
                            </button>
                          </div>
                        </div>
                        <div class="flex flex-wrap gap-2">
                          <label
                            v-for="permission in permissions"
                            :key="permission.id"
                            class="inline-flex items-center cursor-pointer"
                          >
                            <input
                              v-model="form.permissions"
                              :value="permission.id"
                              type="checkbox"
                              class="sr-only"
                            />
                            <span
                              :class="[
                                'inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium transition-colors duration-200',
                                form.permissions.includes(permission.id)
                                  ? 'bg-purple-100 text-purple-800 ring-2 ring-purple-500'
                                  : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'
                              ]"
                            >
                              <svg
                                v-if="form.permissions.includes(permission.id)"
                                class="w-3 h-3 mr-1.5"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                              >
                                <path
                                  fill-rule="evenodd"
                                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                  clip-rule="evenodd"
                                />
                              </svg>
                              {{ formatPermissionName(permission.name) }}
                            </span>
                          </label>
                        </div>
                        <div class="mt-3 text-xs text-gray-500">
                          {{ permissions.length }} permissions selected
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Member Attributes -->
                  <div v-if="groupedPermissions['Member Attributes']" class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                      <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <div class="w-3 h-3 rounded-full bg-orange-500 mr-3"></div>
                        Member Attributes
                      </h3>
                      <div class="flex items-center space-x-4">
                        <button
                          type="button"
                          @click="selectCategoryPermissions('Member Attributes')"
                          class="text-sm text-orange-600 hover:text-orange-500 font-medium"
                        >
                          Select All
                        </button>
                        <button
                          type="button"
                          @click="clearCategoryPermissions('Member Attributes')"
                          class="text-sm text-gray-600 hover:text-gray-500 font-medium"
                        >
                          Clear All
                        </button>
                      </div>
                    </div>
                    <div class="space-y-4">
                      <div
                        v-for="(permissions, module) in groupedPermissions['Member Attributes']"
                        :key="`Member Attributes-${module}`"
                        class="border border-gray-100 rounded-lg p-4 bg-gray-50"
                      >
                        <div class="flex items-center justify-between mb-3">
                          <h4 class="text-base font-medium text-gray-800">
                            {{ module }}
                          </h4>
                          <div class="flex items-center space-x-2">
                            <button
                              type="button"
                              @click="selectModulePermissions('Member Attributes', module)"
                              class="text-xs text-orange-600 hover:text-orange-500 font-medium"
                            >
                              Select All
                            </button>
                            <span class="text-gray-300">|</span>
                            <button
                              type="button"
                              @click="clearModulePermissions('Member Attributes', module)"
                              class="text-xs text-gray-600 hover:text-gray-500 font-medium"
                            >
                              Clear All
                            </button>
                          </div>
                        </div>
                        <div class="flex flex-wrap gap-2">
                          <label
                            v-for="permission in permissions"
                            :key="permission.id"
                            class="inline-flex items-center cursor-pointer"
                          >
                            <input
                              v-model="form.permissions"
                              :value="permission.id"
                              type="checkbox"
                              class="sr-only"
                            />
                            <span
                              :class="[
                                'inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium transition-colors duration-200',
                                form.permissions.includes(permission.id)
                                  ? 'bg-orange-100 text-orange-800 ring-2 ring-orange-500'
                                  : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'
                              ]"
                            >
                              <svg
                                v-if="form.permissions.includes(permission.id)"
                                class="w-3 h-3 mr-1.5"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                              >
                                <path
                                  fill-rule="evenodd"
                                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                  clip-rule="evenodd"
                                />
                              </svg>
                              {{ formatPermissionName(permission.name) }}
                            </span>
                          </label>
                        </div>
                        <div class="mt-3 text-xs text-gray-500">
                          {{ permissions.length }} permissions selected
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Geographic Data -->
                  <div v-if="groupedPermissions['Geographic Data']" class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                      <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <div class="w-3 h-3 rounded-full bg-teal-500 mr-3"></div>
                        Geographic Data
                      </h3>
                      <div class="flex items-center space-x-4">
                        <button
                          type="button"
                          @click="selectCategoryPermissions('Geographic Data')"
                          class="text-sm text-teal-600 hover:text-teal-500 font-medium"
                        >
                          Select All
                        </button>
                        <button
                          type="button"
                          @click="clearCategoryPermissions('Geographic Data')"
                          class="text-sm text-gray-600 hover:text-gray-500 font-medium"
                        >
                          Clear All
                        </button>
                      </div>
                    </div>
                    <div class="space-y-4">
                      <div
                        v-for="(permissions, module) in groupedPermissions['Geographic Data']"
                        :key="`Geographic Data-${module}`"
                        class="border border-gray-100 rounded-lg p-4 bg-gray-50"
                      >
                        <div class="flex items-center justify-between mb-3">
                          <h4 class="text-base font-medium text-gray-800">
                            {{ module }}
                          </h4>
                          <div class="flex items-center space-x-2">
                            <button
                              type="button"
                              @click="selectModulePermissions('Geographic Data', module)"
                              class="text-xs text-teal-600 hover:text-teal-500 font-medium"
                            >
                              Select All
                            </button>
                            <span class="text-gray-300">|</span>
                            <button
                              type="button"
                              @click="clearModulePermissions('Geographic Data', module)"
                              class="text-xs text-gray-600 hover:text-gray-500 font-medium"
                            >
                              Clear All
                            </button>
                          </div>
                        </div>
                        <div class="flex flex-wrap gap-2">
                          <label
                            v-for="permission in permissions"
                            :key="permission.id"
                            class="inline-flex items-center cursor-pointer"
                          >
                            <input
                              v-model="form.permissions"
                              :value="permission.id"
                              type="checkbox"
                              class="sr-only"
                            />
                            <span
                              :class="[
                                'inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium transition-colors duration-200',
                                form.permissions.includes(permission.id)
                                  ? 'bg-teal-100 text-teal-800 ring-2 ring-teal-500'
                                  : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'
                              ]"
                            >
                              <svg
                                v-if="form.permissions.includes(permission.id)"
                                class="w-3 h-3 mr-1.5"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                              >
                                <path
                                  fill-rule="evenodd"
                                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                  clip-rule="evenodd"
                                />
                              </svg>
                              {{ formatPermissionName(permission.name) }}
                            </span>
                          </label>
                        </div>
                        <div class="mt-3 text-xs text-gray-500">
                          {{ permissions.length }} permissions selected
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- System Management -->
                  <div v-if="groupedPermissions['System Management']" class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                      <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <div class="w-3 h-3 rounded-full bg-red-500 mr-3"></div>
                        System Management
                      </h3>
                      <div class="flex items-center space-x-4">
                        <button
                          type="button"
                          @click="selectCategoryPermissions('System Management')"
                          class="text-sm text-red-600 hover:text-red-500 font-medium"
                        >
                          Select All
                        </button>
                        <button
                          type="button"
                          @click="clearCategoryPermissions('System Management')"
                          class="text-sm text-gray-600 hover:text-gray-500 font-medium"
                        >
                          Clear All
                        </button>
                      </div>
                    </div>
                    <div class="space-y-4">
                      <div
                        v-for="(permissions, module) in groupedPermissions['System Management']"
                        :key="`System Management-${module}`"
                        class="border border-gray-100 rounded-lg p-4 bg-gray-50"
                      >
                        <div class="flex items-center justify-between mb-3">
                          <h4 class="text-base font-medium text-gray-800">
                            {{ module }}
                          </h4>
                          <div class="flex items-center space-x-2">
                            <button
                              type="button"
                              @click="selectModulePermissions('System Management', module)"
                              class="text-xs text-red-600 hover:text-red-500 font-medium"
                            >
                              Select All
                            </button>
                            <span class="text-gray-300">|</span>
                            <button
                              type="button"
                              @click="clearModulePermissions('System Management', module)"
                              class="text-xs text-gray-600 hover:text-gray-500 font-medium"
                            >
                              Clear All
                            </button>
                          </div>
                        </div>
                        <div class="flex flex-wrap gap-2">
                          <label
                            v-for="permission in permissions"
                            :key="permission.id"
                            class="inline-flex items-center cursor-pointer"
                          >
                            <input
                              v-model="form.permissions"
                              :value="permission.id"
                              type="checkbox"
                              class="sr-only"
                            />
                            <span
                              :class="[
                                'inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium transition-colors duration-200',
                                form.permissions.includes(permission.id)
                                  ? 'bg-red-100 text-red-800 ring-2 ring-red-500'
                                  : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'
                              ]"
                            >
                              <svg
                                v-if="form.permissions.includes(permission.id)"
                                class="w-3 h-3 mr-1.5"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                              >
                                <path
                                  fill-rule="evenodd"
                                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                  clip-rule="evenodd"
                                />
                              </svg>
                              {{ formatPermissionName(permission.name) }}
                            </span>
                          </label>
                        </div>
                        <div class="mt-3 text-xs text-gray-500">
                          {{ permissions.length }} permissions selected
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- AI Assistance -->
                  <div v-if="groupedPermissions['AI Assistance']" class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                      <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <div class="w-3 h-3 rounded-full bg-pink-500 mr-3"></div>
                        AI Assistance
                      </h3>
                      <div class="flex items-center space-x-4">
                        <button
                          type="button"
                          @click="selectCategoryPermissions('AI Assistance')"
                          class="text-sm text-pink-600 hover:text-pink-500 font-medium"
                        >
                          Select All
                        </button>
                        <button
                          type="button"
                          @click="clearCategoryPermissions('AI Assistance')"
                          class="text-sm text-gray-600 hover:text-gray-500 font-medium"
                        >
                          Clear All
                        </button>
                      </div>
                    </div>
                    <div class="space-y-4">
                      <div
                        v-for="(permissions, module) in groupedPermissions['AI Assistance']"
                        :key="`AI Assistance-${module}`"
                        class="border border-gray-100 rounded-lg p-4 bg-gray-50"
                      >
                        <div class="flex items-center justify-between mb-3">
                          <h4 class="text-base font-medium text-gray-800">
                            {{ module }}
                          </h4>
                          <div class="flex items-center space-x-2">
                            <button
                              type="button"
                              @click="selectModulePermissions('AI Assistance', module)"
                              class="text-xs text-pink-600 hover:text-pink-500 font-medium"
                            >
                              Select All
                            </button>
                            <span class="text-gray-300">|</span>
                            <button
                              type="button"
                              @click="clearModulePermissions('AI Assistance', module)"
                              class="text-xs text-gray-600 hover:text-gray-500 font-medium"
                            >
                              Clear All
                            </button>
                          </div>
                        </div>
                        <div class="flex flex-wrap gap-2">
                          <label
                            v-for="permission in permissions"
                            :key="permission.id"
                            class="inline-flex items-center cursor-pointer"
                          >
                            <input
                              v-model="form.permissions"
                              :value="permission.id"
                              type="checkbox"
                              class="sr-only"
                            />
                            <span
                              :class="[
                                'inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium transition-colors duration-200',
                                form.permissions.includes(permission.id)
                                  ? 'bg-pink-100 text-pink-800 ring-2 ring-pink-500'
                                  : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'
                              ]"
                            >
                              <svg
                                v-if="form.permissions.includes(permission.id)"
                                class="w-3 h-3 mr-1.5"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                              >
                                <path
                                  fill-rule="evenodd"
                                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                  clip-rule="evenodd"
                                />
                              </svg>
                              {{ formatPermissionName(permission.name) }}
                            </span>
                          </label>
                        </div>
                        <div class="mt-3 text-xs text-gray-500">
                          {{ permissions.length }} permissions selected
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Fund App Section -->
                <div v-if="groupedPermissions['Fund App']" class="border-2 border-green-200 rounded-lg p-6 bg-green-50">
                  <h2 class="text-2xl font-bold text-green-800 mb-6 text-center">
                    Fund - App
                  </h2>
                  
                  <div class="space-y-4">
                    <div
                      v-for="(permissions, module) in groupedPermissions['Fund App']"
                      :key="`Fund App-${module}`"
                      class="border border-gray-100 rounded-lg p-4 bg-gray-50"
                    >
                      <div class="flex items-center justify-between mb-3">
                        <h3 class="text-base font-medium text-gray-800">
                          {{ module }}
                        </h3>
                        <div class="flex items-center space-x-2">
                          <button
                            type="button"
                            @click="selectModulePermissions('Fund App', module)"
                            class="text-xs text-green-600 hover:text-green-500 font-medium"
                          >
                            Select All
                          </button>
                          <span class="text-gray-300">|</span>
                          <button
                            type="button"
                            @click="clearModulePermissions('Fund App', module)"
                            class="text-xs text-gray-600 hover:text-gray-500 font-medium"
                          >
                            Clear All
                          </button>
                        </div>
                      </div>
                      <div class="flex flex-wrap gap-2">
                        <label
                          v-for="permission in permissions"
                          :key="permission.id"
                          class="inline-flex items-center cursor-pointer"
                        >
                          <input
                            v-model="form.permissions"
                            :value="permission.id"
                            type="checkbox"
                            class="sr-only"
                          />
                          <span
                            :class="[
                              'inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium transition-colors duration-200',
                              form.permissions.includes(permission.id)
                                ? 'bg-green-100 text-green-800 ring-2 ring-green-500'
                                : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'
                            ]"
                          >
                            <svg
                              v-if="form.permissions.includes(permission.id)"
                              class="w-3 h-3 mr-1.5"
                              fill="currentColor"
                              viewBox="0 0 20 20"
                            >
                              <path
                                fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd"
                              />
                            </svg>
                            {{ formatPermissionName(permission.name) }}
                          </span>
                        </label>
                      </div>
                      <div class="mt-3 text-xs text-gray-500">
                        {{ permissions.length }} permissions selected
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Dashboard Section -->
                <div v-if="groupedPermissions['Dashboard']" class="border-2 border-indigo-200 rounded-lg p-6 bg-indigo-50">
                  <h2 class="text-2xl font-bold text-indigo-800 mb-6 text-center">
                    Dashboard
                  </h2>
                  
                  <div class="space-y-4">
                    <div
                      v-for="(permissions, module) in groupedPermissions['Dashboard']"
                      :key="`Dashboard-${module}`"
                      class="border border-gray-100 rounded-lg p-4 bg-gray-50"
                    >
                      <div class="flex items-center justify-between mb-3">
                        <h3 class="text-base font-medium text-gray-800">
                          {{ module }}
                        </h3>
                        <div class="flex items-center space-x-2">
                          <button
                            type="button"
                            @click="selectModulePermissions('Dashboard', module)"
                            class="text-xs text-indigo-600 hover:text-indigo-500 font-medium"
                          >
                            Select All
                          </button>
                          <span class="text-gray-300">|</span>
                          <button
                            type="button"
                            @click="clearModulePermissions('Dashboard', module)"
                            class="text-xs text-gray-600 hover:text-gray-500 font-medium"
                          >
                            Clear All
                          </button>
                        </div>
                      </div>
                      <div class="flex flex-wrap gap-2">
                        <label
                          v-for="permission in permissions"
                          :key="permission.id"
                          class="inline-flex items-center cursor-pointer"
                        >
                          <input
                            v-model="form.permissions"
                            :value="permission.id"
                            type="checkbox"
                            class="sr-only"
                          />
                          <span
                            :class="[
                              'inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium transition-colors duration-200',
                              form.permissions.includes(permission.id)
                                ? 'bg-indigo-100 text-indigo-800 ring-2 ring-indigo-500'
                                : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'
                            ]"
                          >
                            <svg
                              v-if="form.permissions.includes(permission.id)"
                              class="w-3 h-3 mr-1.5"
                              fill="currentColor"
                              viewBox="0 0 20 20"
                            >
                              <path
                                fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd"
                              />
                            </svg>
                            {{ formatPermissionName(permission.name) }}
                          </span>
                        </label>
                      </div>
                      <div class="mt-3 text-xs text-gray-500">
                        {{ permissions.length }} permissions selected
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- No Permissions Message -->
              <div v-else class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No permissions available</h3>
                <p class="mt-1 text-sm text-gray-500">
                  No permissions are currently available to assign.
                </p>
              </div>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="flex items-center justify-end space-x-4 bg-white px-6 py-4 border-t border-gray-200 rounded-lg shadow">
            <button
              type="button"
              @click="goBack"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="form.processing"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg
                v-if="form.processing"
                class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                fill="none"
                viewBox="0 0 24 24"
              >
                <circle
                  class="opacity-25"
                  cx="12"
                  cy="12"
                  r="10"
                  stroke="currentColor"
                  stroke-width="4"
                ></circle>
                <path
                  class="opacity-75"
                  fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
              </svg>
              {{ form.processing ? 'Creating...' : 'Create Role' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';

interface Permission {
  id: number;
  name: string;
  module: string;
}

interface Props {
  permissions: Permission[];
}

const props = defineProps<Props>();

// Form setup
const form = useForm({
  name: '',
  description: '',
  permissions: [] as number[],
});

// Group permissions by category and then by module
const groupedPermissions = computed(() => {
  if (!props.permissions) return null;
  
  return props.permissions.reduce((groups, permission) => {
    const categoryModule = permission.module || 'General → Other';
    const [category, module] = categoryModule.split(' → ');
    
    if (!groups[category]) {
      groups[category] = {};
    }
    if (!groups[category][module]) {
      groups[category][module] = [];
    }
    groups[category][module].push(permission);
    return groups;
  }, {} as Record<string, Record<string, Permission[]>>);
});

// Helper functions
const formatPermissionName = (name: string) => {
  return name
    .replace(/[-_]/g, ' ')
    .replace(/\b\w/g, l => l.toUpperCase());
};

const getSelectedCount = (category: string, module: string) => {
  if (!groupedPermissions.value || !groupedPermissions.value[category] || !groupedPermissions.value[category][module]) return 0;
  return groupedPermissions.value[category][module].filter(permission => 
    form.permissions.includes(permission.id)
  ).length;
};

const getCategorySelectedCount = (category: string) => {
  if (!groupedPermissions.value || !groupedPermissions.value[category]) return 0;
  let count = 0;
  Object.values(groupedPermissions.value[category]).forEach(permissions => {
    count += permissions.filter(permission => form.permissions.includes(permission.id)).length;
  });
  return count;
};

const getCategoryTotalCount = (category: string) => {
  if (!groupedPermissions.value || !groupedPermissions.value[category]) return 0;
  let count = 0;
  Object.values(groupedPermissions.value[category]).forEach(permissions => {
    count += permissions.length;
  });
  return count;
};

const selectAllPermissions = () => {
  form.permissions = props.permissions.map(p => p.id);
};

const clearAllPermissions = () => {
  form.permissions = [];
};

const selectCategoryPermissions = (category: string) => {
  if (!groupedPermissions.value || !groupedPermissions.value[category]) return;
  
  const categoryPermissionIds: number[] = [];
  Object.values(groupedPermissions.value[category]).forEach(permissions => {
    categoryPermissionIds.push(...permissions.map(p => p.id));
  });
  
  const otherPermissions = form.permissions.filter(id => 
    !categoryPermissionIds.includes(id)
  );
  form.permissions = [...otherPermissions, ...categoryPermissionIds];
};

const clearCategoryPermissions = (category: string) => {
  if (!groupedPermissions.value || !groupedPermissions.value[category]) return;
  
  const categoryPermissionIds: number[] = [];
  Object.values(groupedPermissions.value[category]).forEach(permissions => {
    categoryPermissionIds.push(...permissions.map(p => p.id));
  });
  
  form.permissions = form.permissions.filter(id => 
    !categoryPermissionIds.includes(id)
  );
};

const selectModulePermissions = (category: string, module: string) => {
  if (!groupedPermissions.value || !groupedPermissions.value[category] || !groupedPermissions.value[category][module]) return;
  
  const modulePermissionIds = groupedPermissions.value[category][module].map(p => p.id);
  const otherPermissions = form.permissions.filter(id => 
    !modulePermissionIds.includes(id)
  );
  form.permissions = [...otherPermissions, ...modulePermissionIds];
};

const clearModulePermissions = (category: string, module: string) => {
  if (!groupedPermissions.value || !groupedPermissions.value[category] || !groupedPermissions.value[category][module]) return;
  
  const modulePermissionIds = groupedPermissions.value[category][module].map(p => p.id);
  form.permissions = form.permissions.filter(id => 
    !modulePermissionIds.includes(id)
  );
};

const submitForm = () => {
  form.post(route('roles.store'), {
    onSuccess: () => {
      router.visit(route('roles.index'));
    },
  });
};

const goBack = () => {
  router.visit(route('roles.index'));
};
</script>
