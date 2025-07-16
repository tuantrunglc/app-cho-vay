<template>
  <div class="api-example">
    <h2>API Service Usage Example</h2>
    
    <!-- Loading state -->
    <div v-if="loading" class="loading">
      Đang tải...
    </div>

    <!-- Error state -->
    <div v-if="error" class="error">
      Lỗi: {{ error }}
    </div>

    <!-- Success state -->
    <div v-if="data" class="success">
      <h3>Dữ liệu từ API:</h3>
      <pre>{{ JSON.stringify(data, null, 2) }}</pre>
    </div>

    <!-- Action buttons -->
    <div class="actions">
      <button @click="fetchData" :disabled="loading">
        Lấy dữ liệu
      </button>
      <button @click="postData" :disabled="loading">
        Gửi dữ liệu
      </button>
      <button @click="clearData">
        Xóa dữ liệu
      </button>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue'
import apiService from '@/services/apiService.js'

export default {
  name: 'ApiUsageExample',
  setup() {
    const loading = ref(false)
    const error = ref(null)
    const data = ref(null)

    // Hàm lấy dữ liệu từ API
    const fetchData = async () => {
      loading.value = true
      error.value = null
      
      try {
        // Ví dụ: Lấy danh sách loans
        const response = await apiService.getLoans()
        data.value = response
      } catch (err) {
        error.value = err.message || 'Có lỗi xảy ra khi lấy dữ liệu'
        console.error('API Error:', err)
      } finally {
        loading.value = false
      }
    }

    // Hàm gửi dữ liệu lên API
    const postData = async () => {
      loading.value = true
      error.value = null
      
      try {
        // Ví dụ: Tạo loan mới
        const loanData = {
          amount: 10000000,
          term: 12,
          purpose: 'Mua xe'
        }
        
        const response = await apiService.createLoan(loanData)
        data.value = response
      } catch (err) {
        error.value = err.message || 'Có lỗi xảy ra khi gửi dữ liệu'
        console.error('API Error:', err)
      } finally {
        loading.value = false
      }
    }

    // Hàm xóa dữ liệu
    const clearData = () => {
      data.value = null
      error.value = null
    }

    return {
      loading,
      error,
      data,
      fetchData,
      postData,
      clearData
    }
  }
}
</script>

<style scoped>
.api-example {
  padding: 20px;
  max-width: 600px;
  margin: 0 auto;
}

.loading {
  color: #007bff;
  font-weight: bold;
  margin: 10px 0;
}

.error {
  color: #dc3545;
  background-color: #f8d7da;
  border: 1px solid #f5c6cb;
  padding: 10px;
  border-radius: 4px;
  margin: 10px 0;
}

.success {
  color: #155724;
  background-color: #d4edda;
  border: 1px solid #c3e6cb;
  padding: 10px;
  border-radius: 4px;
  margin: 10px 0;
}

.success pre {
  background-color: #f8f9fa;
  padding: 10px;
  border-radius: 4px;
  overflow-x: auto;
  margin-top: 10px;
}

.actions {
  margin-top: 20px;
}

.actions button {
  background-color: #007bff;
  color: white;
  border: none;
  padding: 10px 15px;
  margin-right: 10px;
  border-radius: 4px;
  cursor: pointer;
}

.actions button:hover:not(:disabled) {
  background-color: #0056b3;
}

.actions button:disabled {
  background-color: #6c757d;
  cursor: not-allowed;
}
</style>