<template>
  <div class="p-4 space-y-4">
    <!-- Header -->
    <div class="flex items-center space-x-3 mb-4">
      <button @click="$router.go(-1)" class="p-2 hover:bg-gray-100 rounded-lg">
        <ArrowLeftIcon class="w-5 h-5 text-gray-600" />
      </button>
      <h1 class="text-xl font-bold text-gray-900">Thông tin cá nhân</h1>
    </div>

    <!-- Profile Photo -->
    <div class="bg-white rounded-xl shadow-sm p-4 text-center">
      <div class="relative inline-block">
        <div class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-3">
          <UserIcon class="w-10 h-10 text-gray-400" />
        </div>
        <button class="absolute bottom-0 right-0 w-6 h-6 bg-primary-600 rounded-full flex items-center justify-center">
          <PencilIcon class="w-3 h-3 text-white" />
        </button>
      </div>
      <h3 class="font-semibold text-gray-900">{{ userInfo.fullName }}</h3>
      <p class="text-sm text-gray-600">ID: {{ userInfo.userId }}</p>
    </div>

    <!-- Personal Information Form -->
    <form @submit.prevent="updatePersonalInfo" class="space-y-4">
      <!-- Basic Info -->
      <div class="bg-white rounded-xl shadow-sm p-4">
        <h2 class="text-base font-semibold text-gray-900 mb-4">Thông tin cơ bản</h2>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Họ và tên *
            </label>
            <input
              v-model="form.fullName"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-base"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Số điện thoại *
            </label>
            <input
              v-model="form.phone"
              type="tel"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-base"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Email
            </label>
            <input
              v-model="form.email"
              type="email"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-base"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Ngày sinh *
            </label>
            <input
              v-model="form.dateOfBirth"
              type="date"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-base"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Giới tính *
            </label>
            <select
              v-model="form.gender"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-base"
              required
            >
              <option value="">Chọn giới tính</option>
              <option value="male">Nam</option>
              <option value="female">Nữ</option>
              <option value="other">Khác</option>
            </select>
          </div>
        </div>
      </div>

      <!-- ID Information -->
      <div class="bg-white rounded-xl shadow-sm p-4">
        <h2 class="text-base font-semibold text-gray-900 mb-4">Thông tin CMND/CCCD</h2>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Số CMND/CCCD *
            </label>
            <input
              v-model="form.idNumber"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-base"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Ngày cấp *
            </label>
            <input
              v-model="form.idIssueDate"
              type="date"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-base"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Nơi cấp *
            </label>
            <input
              v-model="form.idIssuePlace"
              type="text"
              placeholder="Ví dụ: Công an TP. Hồ Chí Minh"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-base"
              required
            />
          </div>
        </div>
      </div>

      <!-- Address Information -->
      <div class="bg-white rounded-xl shadow-sm p-4">
        <h2 class="text-base font-semibold text-gray-900 mb-4">Địa chỉ</h2>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Tỉnh/Thành phố *
            </label>
            <select
              v-model="form.province"
              @change="onProvinceChange"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-base"
              required
            >
              <option value="">Chọn tỉnh/thành phố</option>
              <option value="ho-chi-minh">TP. Hồ Chí Minh</option>
              <option value="ha-noi">Hà Nội</option>
              <option value="da-nang">Đà Nẵng</option>
              <option value="can-tho">Cần Thơ</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Quận/Huyện *
            </label>
            <select
              v-model="form.district"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-base"
              required
            >
              <option value="">Chọn quận/huyện</option>
              <option v-for="district in districts" :key="district.value" :value="district.value">
                {{ district.label }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Phường/Xã *
            </label>
            <select
              v-model="form.ward"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-base"
              required
            >
              <option value="">Chọn phường/xã</option>
              <option v-for="ward in wards" :key="ward.value" :value="ward.value">
                {{ ward.label }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Địa chỉ chi tiết *
            </label>
            <textarea
              v-model="form.address"
              rows="3"
              placeholder="Số nhà, tên đường..."
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-base resize-none"
              required
            ></textarea>
          </div>
        </div>
      </div>

      <!-- Employment Information -->
      <div class="bg-white rounded-xl shadow-sm p-4">
        <h2 class="text-base font-semibold text-gray-900 mb-4">Thông tin công việc</h2>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Nghề nghiệp *
            </label>
            <select
              v-model="form.occupation"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-base"
              required
            >
              <option value="">Chọn nghề nghiệp</option>
              <option value="employee">Nhân viên văn phòng</option>
              <option value="business">Kinh doanh</option>
              <option value="freelancer">Tự do</option>
              <option value="student">Sinh viên</option>
              <option value="other">Khác</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Thu nhập hàng tháng *
            </label>
            <select
              v-model="form.monthlyIncome"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-base"
              required
            >
              <option value="">Chọn mức thu nhập</option>
              <option value="under-5m">Dưới 5 triệu</option>
              <option value="5m-10m">5 - 10 triệu</option>
              <option value="10m-20m">10 - 20 triệu</option>
              <option value="20m-50m">20 - 50 triệu</option>
              <option value="over-50m">Trên 50 triệu</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Tên công ty/Nơi làm việc
            </label>
            <input
              v-model="form.company"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-base"
            />
          </div>
        </div>
      </div>

      <!-- Submit Button -->
      <button
        type="submit"
        class="w-full bg-primary-600 text-white py-3 rounded-xl font-semibold hover:bg-primary-700 transition-colors duration-200"
        :disabled="isUpdating"
      >
        {{ isUpdating ? 'Đang cập nhật...' : 'Cập nhật thông tin' }}
      </button>
    </form>

    <!-- Success Modal -->
    <div v-if="showSuccessModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg p-6 max-w-sm w-full">
        <div class="text-center">
          <div class="w-16 h-16 bg-success-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <CheckIcon class="w-8 h-8 text-success-600" />
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Cập nhật thành công!</h3>
          <p class="text-gray-600 mb-4">Thông tin cá nhân đã được cập nhật</p>
          <button
            @click="showSuccessModal = false"
            class="w-full bg-primary-600 text-white py-2 rounded-lg hover:bg-primary-700 transition-colors duration-200"
          >
            Đóng
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { useRouter } from 'vue-router'
import ArrowLeftIcon from '@/components/icons/ArrowLeftIcon.vue'
import UserIcon from '@/components/icons/UserIcon.vue'
import PencilIcon from '@/components/icons/PencilIcon.vue'
import CheckIcon from '@/components/icons/CheckIcon.vue'

const router = useRouter()

// User info
const userInfo = ref({
  fullName: 'Nguyễn Văn A',
  userId: 'UV001234',
  phone: '0901234567',
  email: 'nguyenvana@email.com'
})

// Form data
const form = reactive({
  fullName: 'Nguyễn Văn A',
  phone: '0901234567',
  email: 'nguyenvana@email.com',
  dateOfBirth: '1990-01-15',
  gender: 'male',
  idNumber: '123456789012',
  idIssueDate: '2020-01-15',
  idIssuePlace: 'Công an TP. Hồ Chí Minh',
  province: 'ho-chi-minh',
  district: 'quan-1',
  ward: 'phuong-ben-nghe',
  address: '123 Đường Nguyễn Huệ',
  occupation: 'employee',
  monthlyIncome: '10m-20m',
  company: 'Công ty ABC'
})

const isUpdating = ref(false)
const showSuccessModal = ref(false)

// Address data
const districts = computed(() => {
  const districtMap = {
    'ho-chi-minh': [
      { value: 'quan-1', label: 'Quận 1' },
      { value: 'quan-3', label: 'Quận 3' },
      { value: 'quan-5', label: 'Quận 5' },
      { value: 'quan-7', label: 'Quận 7' }
    ],
    'ha-noi': [
      { value: 'hoan-kiem', label: 'Hoàn Kiếm' },
      { value: 'ba-dinh', label: 'Ba Đình' },
      { value: 'dong-da', label: 'Đống Đa' }
    ]
  }
  return districtMap[form.province] || []
})

const wards = computed(() => {
  const wardMap = {
    'quan-1': [
      { value: 'phuong-ben-nghe', label: 'Phường Bến Nghé' },
      { value: 'phuong-ben-thanh', label: 'Phường Bến Thành' },
      { value: 'phuong-co-giang', label: 'Phường Cô Giang' }
    ]
  }
  return wardMap[form.district] || []
})

// Methods
const onProvinceChange = () => {
  form.district = ''
  form.ward = ''
}

const updatePersonalInfo = async () => {
  isUpdating.value = true
  
  // Mock API call
  await new Promise(resolve => setTimeout(resolve, 2000))
  
  // Update user info
  userInfo.value.fullName = form.fullName
  userInfo.value.phone = form.phone
  userInfo.value.email = form.email
  
  isUpdating.value = false
  showSuccessModal.value = true
}
</script>