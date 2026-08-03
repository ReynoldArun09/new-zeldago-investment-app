<!-- Send Notification Modal -->
<div x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div x-show="openModal" x-transition.opacity @click="openModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-900/75 backdrop-blur-sm"></div>
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal panel -->
        <div x-show="openModal" x-transition.scale.origin.bottom class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <form action="{{ route('admin.notifications.send') }}" method="POST" x-data="{ target: 'all' }">
                @csrf
                <div class="bg-white px-6 pt-5 pb-4">
                    <h3 class="text-xl font-bold text-gray-800 mb-4" id="modal-title">
                        Send Notification
                    </h3>
                    <hr class="border-gray-100 mb-4 -mx-6">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Send To <span class="text-red-500">*</span></label>
                            <select name="target" x-model="target" required class="w-full rounded-lg border-gray-300 focus:border-[var(--theme-primary)] text-sm p-3">
                                <option value="all">All Users</option>
                                <option value="investors">All Investors</option>
                                <option value="agents">All Agents</option>
                                <option value="specific">Specific User</option>
                            </select>
                        </div>
                        
                        <div x-show="target === 'specific'" style="display: none;">
                            <label class="block text-sm text-gray-600 mb-1">User ID <span class="text-red-500">*</span></label>
                            <input type="number" name="target_user_id" :required="target === 'specific'" class="w-full rounded-lg border-gray-300 focus:border-[var(--theme-primary)] text-sm p-3" placeholder="Enter User ID">
                        </div>

                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Title <span class="text-red-500">*</span></label>
                            <input type="text" name="title" required class="w-full rounded-lg border-gray-300 focus:border-[var(--theme-primary)] text-sm p-3" placeholder="Notification Title">
                        </div>

                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Message <span class="text-red-500">*</span></label>
                            <textarea name="message" required rows="4" class="w-full rounded-lg border-gray-300 focus:border-[var(--theme-primary)] text-sm p-3 outline-none" placeholder="Type your message here..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="bg-white px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                    <button type="submit" class="inline-flex justify-center rounded-lg border border-transparent px-4 py-2 bg-[var(--theme-primary)] text-sm font-medium text-white hover:opacity-90 focus:outline-none transition-opacity">
                        Send Notification
                    </button>
                    <button type="button" @click="openModal = false" class="inline-flex justify-center rounded-lg border border-gray-300 px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
