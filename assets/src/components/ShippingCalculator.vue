<template>
  <div class="shipping-container">
    <h1>Shipping Cost Calculator</h1>

    <div class="form-group">
      <label for="carrier">Carrier:</label>
      <select
        id="carrier"
        v-model="carrier"
        :disabled="loading"
      >
        <option value="">Select a carrier</option>
        <option
          v-for="c in availableCarriers"
          :key="c"
          :value="c"
        >
          {{ c }}
        </option>
      </select>
    </div>

    <div class="form-group">
      <label for="weight">Weight (kg):</label>
      <input
        id="weight"
        type="number"
        step="0.01"
        min="0.01"
        v-model="weightKg"
        placeholder="Enter parcel weight"
        :disabled="loading"
      >
    </div>

    <div class="btn-group">
      <button
        class="btn-calculate"
        @click="calculate"
        :disabled="loading || !carrier || !weightKg"
      >
        {{ loading ? 'Calculating...' : 'Calculate price' }}
      </button>
      <button
        class="btn-reset"
        @click="reset"
        :disabled="loading"
      >
        Reset
      </button>
    </div>

    <div v-if="result" class="result">
      <h3>Shipping Cost</h3>
      <div class="price">{{ result.price }} {{ result.currency }}</div>
      <div><strong>Carrier:</strong> {{ result.carrier }}</div>
      <div><strong>Weight:</strong> {{ result.weightKg }} kg</div>
    </div>

    <div v-if="error" class="error">
      {{ error }}
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const api = axios.create({
  baseURL: '/api',
  headers: {
    'Content-Type': 'application/json'
  }
})

const carrier = ref('')
const weightKg = ref('')
const availableCarriers = ref([])
const loading = ref(false)
const result = ref(null)
const error = ref(null)

const fetchCarriers = async () => {
  try {
    const response = await api.get('/carriers')
    availableCarriers.value = response.data.carriers
  } catch (e) {
    error.value = 'Failed to load carriers.'
  }
}

const calculate = async () => {
  loading.value = true
  result.value = null
  error.value = null

  try {
    const response = await api.post('/shipping/calculate', {
      carrier: carrier.value,
      weightKg: parseFloat(weightKg.value)
    })

    result.value = response.data
  } catch (e) {
    if (e.response?.data?.error) {
      error.value = e.response.data.error
    } else if (e.response?.data?.errors) {
      error.value = e.response.data.errors.map(err => err.message).join(', ')
    } else {
      error.value = 'Network error. Please try again.'
    }
  } finally {
    loading.value = false
  }
}

const reset = () => {
  carrier.value = ''
  weightKg.value = ''
  result.value = null
  error.value = null
}

onMounted(() => {
  fetchCarriers()
})
</script>

<style scoped>
.shipping-container {
  max-width: 500px;
  width: 100%;
  padding: 30px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
}

.shipping-container h1 {
  text-align: center;
  margin-bottom: 30px;
  color: #333;
  font-size: 28px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 500;
  color: #555;
  font-size: 14px;
}

.form-group select,
.form-group input {
  width: 100%;
  padding: 12px;
  border: 2px solid #e0e0e0;
  border-radius: 6px;
  font-size: 16px;
  transition: border-color 0.3s;
}

.form-group select:focus,
.form-group input:focus {
  outline: none;
  border-color: #667eea;
}

.btn-group {
  display: flex;
  gap: 10px;
  margin-top: 25px;
}

.btn-group button {
  flex: 1;
  padding: 14px;
  border: none;
  border-radius: 6px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-calculate {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.btn-calculate:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.btn-calculate:disabled {
  background: #cccccc;
  cursor: not-allowed;
  transform: none;
}

.btn-reset {
  background-color: #f44336;
  color: white;
}

.btn-reset:hover:not(:disabled) {
  background-color: #da190b;
}

.result {
  margin-top: 30px;
  padding: 25px;
  background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
  border-radius: 8px;
  border-left: 4px solid #667eea;
}

.result h3 {
  margin-top: 0;
  color: #333;
  font-size: 18px;
}

.result .price {
  font-size: 36px;
  font-weight: bold;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin: 15px 0;
}

.result div {
  color: #666;
  font-size: 15px;
  margin: 8px 0;
}

.error {
  margin-top: 20px;
  padding: 15px;
  background-color: #ffebee;
  border-radius: 6px;
  border-left: 4px solid #f44336;
  color: #c62828;
}
</style>
