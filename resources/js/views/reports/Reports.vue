<template>
  <div class="container mx-auto px-4 py-8">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Financial Reports</h1>
      <p class="mt-2 text-gray-600">Generate and download financial reports in PDF or Excel format.</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6 mb-8">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Report Type -->
        <div class="md:col-span-2">
          <label for="report-type" class="block text-sm font-medium text-gray-700">Report Type</label>
          <select 
            id="report-type" 
            v-model="form.type" 
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
            @change="onReportTypeChange"
          >
            <option v-for="report in availableReports" :key="report.id" :value="report.id">
              {{ report.name }}
            </option>
          </select>
          <p class="mt-1 text-xs text-gray-500">
            {{ selectedReport?.description || 'Select a report type' }}
          </p>
        </div>

        <!-- Format -->
        <div>
          <label for="format" class="block text-sm font-medium text-gray-700">Format</label>
          <select 
            id="format" 
            v-model="form.format" 
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
          >
            <option v-for="format in selectedReport?.formats || []" :key="format" :value="format">
              {{ format.toUpperCase() }}
            </option>
          </select>
        </div>

        <!-- Time Frame Selector -->
        <div>
          <label for="time-frame" class="block text-sm font-medium text-gray-700">Time Frame</label>
          <select 
            id="time-frame" 
            v-model="form.timeFrame" 
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
            @change="onTimeFrameChange"
          >
            <option 
              v-for="timeFrame in selectedReport?.time_frames || []" 
              :key="timeFrame" 
              :value="timeFrame"
            >
              {{ formatTimeFrame(timeFrame) }}
            </option>
          </select>
        </div>

        <!-- Month Picker (conditionally shown) -->
        <div v-if="showMonthPicker">
          <label for="month" class="block text-sm font-medium text-gray-700">Month</label>
          <input 
            type="month" 
            id="month"
            v-model="form.month" 
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
          />
        </div>

        <!-- Year Picker (conditionally shown) -->
        <div v-if="showYearPicker">
          <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
          <select 
            id="year" 
            v-model="form.year" 
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
          >
            <option v-for="year in years" :key="year" :value="year">
              {{ year }}
            </option>
          </select>
        </div>

        <!-- Date Range (conditionally shown) -->
        <template v-if="showDateRange">
          <div>
            <label for="start-date" class="block text-sm font-medium text-gray-700">Start Date</label>
            <input 
              type="date" 
              id="start-date" 
              v-model="form.startDate" 
              class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
            />
          </div>
          <div>
            <label for="end-date" class="block text-sm font-medium text-gray-700">End Date</label>
            <input 
              type="date" 
              id="end-date" 
              v-model="form.endDate" 
              :min="form.startDate"
              class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
            />
          </div>
        </template>
      </div>

      <div class="mt-6 flex justify-end">
        <button 
          @click="generateReport" 
          :disabled="isGenerating"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
        >
          <svg v-if="isGenerating" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ isGenerating ? 'Generating...' : 'Generate Report' }}
        </button>
      </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
      <h2 class="text-lg font-medium text-gray-900 mb-4">Report History</h2>
      
      <div v-if="recentReports.length > 0">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Report</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                <th scope="col" class="relative px-6 py-3">
                  <span class="sr-only">Actions</span>
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="report in recentReports" :key="report.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ report.name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatReportType(report.type) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(report.generated_at) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <a :href="report.download_url" class="text-blue-600 hover:text-blue-900 mr-3">Download</a>
                  <button class="text-red-600 hover:text-red-900" @click="confirmDelete(report)">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <div class="mt-4 flex justify-between items-center">
          <div class="text-sm text-gray-700">
            Showing <span class="font-medium">{{ pagination.from }}</span> to <span class="font-medium">{{ pagination.to }}</span> of <span class="font-medium">{{ pagination.total }}</span> reports
          </div>
          
          <div class="flex space-x-2">
            <button 
              @click="fetchReports(pagination.current_page - 1)" 
              :disabled="pagination.current_page === 1"
              class="px-3 py-1 border rounded text-sm font-medium"
              :class="pagination.current_page === 1 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'text-gray-700 hover:bg-gray-50'"
            >
              Previous
            </button>
            
            <button 
              @click="fetchReports(pagination.current_page + 1)" 
              :disabled="pagination.current_page === pagination.last_page"
              class="px-3 py-1 border rounded text-sm font-medium"
              :class="pagination.current_page === pagination.last_page ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'text-gray-700 hover:bg-gray-50'"
            >
              Next
            </button>
          </div>
        </div>
      </div>
      
      <div v-else class="text-center py-8 text-gray-500">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No reports yet</h3>
        <p class="mt-1 text-sm text-gray-500">Generate a report to see it here.</p>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { format } from 'date-fns';

export default {
  name: 'Reports',
  
  setup() {
    const router = useRouter();
    
    // Available report types
    const availableReports = ref([]);
    
    // Form data
    const form = ref({
      type: 'monthly',
      format: 'pdf',
      timeFrame: 'monthly',
      month: format(new Date(), 'yyyy-MM'),
      year: new Date().getFullYear(),
      startDate: format(new Date().setDate(1), 'yyyy-MM-dd'),
      endDate: format(new Date(), 'yyyy-MM-dd'),
    });
    
    // Report history
    const recentReports = ref([]);
    const pagination = ref({
      current_page: 1,
      last_page: 1,
      per_page: 10,
      from: 0,
      to: 0,
      total: 0,
    });
    
    // UI state
    const isGenerating = ref(false);
    const isLoading = ref(false);
    
    // Generate years for the year selector (last 10 years + next 5 years)
    const currentYear = new Date().getFullYear();
    const years = Array.from({ length: 15 }, (_, i) => currentYear - 5 + i);
    
    // Computed properties
    const selectedReport = computed(() => {
      return availableReports.value.find(r => r.id === form.value.type) || {};
    });
    
    const showMonthPicker = computed(() => {
      return form.value.timeFrame === 'monthly' && 
             selectedReport.value.time_frames?.includes('monthly');
    });
    
    const showYearPicker = computed(() => {
      return form.value.timeFrame === 'annual' && 
             selectedReport.value.time_frames?.includes('annual');
    });
    
    const showDateRange = computed(() => {
      return form.value.timeFrame === 'custom' && 
             selectedReport.value.time_frames?.includes('custom');
    });
    
    // Methods
    const fetchAvailableReports = async () => {
      try {
        const response = await axios.get('/api/reports');
        availableReports.value = response.data.reports;
        
        // Set default values based on the first available report
        if (availableReports.value.length > 0) {
          form.value.type = availableReports.value[0].id;
          form.value.format = availableReports.value[0].formats[0];
          form.value.timeFrame = availableReports.value[0].time_frames[0];
        }
      } catch (error) {
        console.error('Failed to fetch available reports:', error);
      }
    };
    
    const fetchReports = async (page = 1) => {
      isLoading.value = true;
      try {
        const response = await axios.get(`/api/reports/history?page=${page}`);
        recentReports.value = response.data.data;
        
        // Update pagination info
        pagination.value = {
          current_page: response.data.current_page,
          last_page: response.data.last_page,
          per_page: response.data.per_page,
          from: response.data.from,
          to: response.data.to,
          total: response.data.total,
        };
      } catch (error) {
        console.error('Failed to fetch report history:', error);
      } finally {
        isLoading.value = false;
      }
    };
    
    const generateReport = async () => {
      if (isGenerating.value) return;
      
      isGenerating.value = true;
      
      try {
        const params = {
          type: form.value.type,
          format: form.value.format,
        };
        
        // Add appropriate date parameters based on time frame
        if (showMonthPicker.value) {
          params.month = form.value.month;
          
          // Also set start_date and end_date for the selected month
          const [year, month] = form.value.month.split('-');
          const startDate = new Date(year, month - 1, 1);
          const endDate = new Date(year, month, 0);
          
          params.start_date = format(startDate, 'yyyy-MM-dd');
          params.end_date = format(endDate, 'yyyy-MM-dd');
        } else if (showYearPicker.value) {
          params.year = form.value.year;
          
          // Set start_date and end_date for the selected year
          const startDate = new Date(form.value.year, 0, 1);
          const endDate = new Date(form.value.year, 11, 31);
          
          params.start_date = format(startDate, 'yyyy-MM-dd');
          params.end_date = format(endDate, 'yyyy-MM-dd');
        } else if (showDateRange.value) {
          params.start_date = form.value.startDate;
          params.end_date = form.value.endDate || form.value.startDate;
        }
        
        const response = await axios.post('/api/reports/generate', params);
        
        // Show success message
        alert('Report generated successfully!');
        
        // Refresh the reports list
        await fetchReports();
        
        // Automatically download the report
        if (response.data.download_url) {
          window.open(response.data.download_url, '_blank');
        }
        
      } catch (error) {
        console.error('Failed to generate report:', error);
        
        let errorMessage = 'Failed to generate report. Please try again.';
        if (error.response?.data?.message) {
          errorMessage = error.response.data.message;
        }
        
        alert(errorMessage);
      } finally {
        isGenerating.value = false;
      }
    };
    
    const confirmDelete = (report) => {
      if (confirm(`Are you sure you want to delete the report "${report.name}"?`)) {
        deleteReport(report.id);
      }
    };
    
    const deleteReport = async (reportId) => {
      try {
        await axios.delete(`/api/reports/${reportId}`);
        
        // Remove the report from the list
        recentReports.value = recentReports.value.filter(r => r.id !== reportId);
        
        // Show success message
        alert('Report deleted successfully!');
        
        // Refresh the reports list if the current page becomes empty
        if (recentReports.value.length === 0 && pagination.value.current_page > 1) {
          await fetchReports(pagination.value.current_page - 1);
        } else {
          await fetchReports(pagination.value.current_page);
        }
      } catch (error) {
        console.error('Failed to delete report:', error);
        alert('Failed to delete report. Please try again.');
      }
    };
    
    const onReportTypeChange = () => {
      // Reset format to the first available format for the selected report type
      if (selectedReport.value.formats && selectedReport.value.formats.length > 0) {
        form.value.format = selectedReport.value.formats[0];
      }
      
      // Reset time frame to the first available for the selected report type
      if (selectedReport.value.time_frames && selectedReport.value.time_frames.length > 0) {
        form.value.timeFrame = selectedReport.value.time_frames[0];
      }
      
      // Reset dates when report type changes
      resetDates();
    };
    
    const onTimeFrameChange = () => {
      resetDates();
    };
    
    const resetDates = () => {
      const now = new Date();
      
      if (form.value.timeFrame === 'monthly') {
        form.value.month = format(now, 'yyyy-MM');
      } else if (form.value.timeFrame === 'annual') {
        form.value.year = now.getFullYear();
      } else if (form.value.timeFrame === 'custom') {
        form.value.startDate = format(now.setDate(1), 'yyyy-MM-dd');
        form.value.endDate = format(new Date(), 'yyyy-MM-dd');
      }
    };
    
    const formatDate = (dateString) => {
      return format(new Date(dateString), 'MMM d, yyyy h:mm a');
    };
    
    const formatReportType = (type) => {
      return type
        .split('-')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
    };
    
    const formatTimeFrame = (timeFrame) => {
      switch (timeFrame) {
        case 'monthly': return 'This Month';
        case 'annual': return 'This Year';
        case 'custom': return 'Custom Range';
        case 'all': return 'All Time';
        default: return timeFrame;
      }
    };
    
    // Lifecycle hooks
    onMounted(() => {
      fetchAvailableReports();
      fetchReports();
      
      // Set default end date to today if not set
      if (!form.value.endDate) {
        form.value.endDate = format(new Date(), 'yyyy-MM-dd');
      }
    });
    
    return {
      // State
      availableReports,
      form,
      recentReports,
      pagination,
      isGenerating,
      isLoading,
      years,
      
      // Computed
      selectedReport,
      showMonthPicker,
      showYearPicker,
      showDateRange,
      
      // Methods
      generateReport,
      confirmDelete,
      deleteReport,
      fetchReports,
      onReportTypeChange,
      onTimeFrameChange,
      formatDate,
      formatReportType,
      formatTimeFrame,
    };
  },
};
</script>

<style scoped>
/* Add any component-specific styles here */
</style>
