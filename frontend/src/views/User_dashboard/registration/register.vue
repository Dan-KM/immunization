<script setup>
import registrationForm from '@/components/User/registrationForm.vue';
import FormInput from '@/components/User/formInput.vue';
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import userStore from '@/stores/userStore';

const store = userStore();

const identifier = ref('');
const email = ref('');
const phone_number = ref('');
const password = ref('');
const errors = ref({
  identifier: null,
  password: null
});

const isLoading = ref(false);

const router = useRouter();

const updateField = () => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  const phoneRegex = /^\+?\d{10,15}$/;

  if (emailRegex.test(identifier.value)) {
    email.value = identifier.value;
    phone_number.value = '';
    errors.value.identifier = null;
  } else if (phoneRegex.test(identifier.value)) {
    phone_number.value = identifier.value;
    email.value = '';
    errors.value.identifier = null;
  } else {
    email.value = '';
    phone_number.value = '';
    if (identifier.value) {
      errors.value.identifier = "Please enter a valid email or phone number";
    } else {
      errors.value.identifier = null;
    }
  }
};

const handleSubmit = async () => {
  updateField();
  
  if (!identifier.value) {
    errors.value.identifier = "Please enter your email or phone number";
  }
  
  if (!password.value) {
    errors.value.password = "Please enter your ID number";
  } else if (password.value.length < 8) {
    errors.value.password = "ID number must be at least 8 characters";
  } else {
    errors.value.password = null;
  }

  if (!errors.value.identifier && !errors.value.password) {
    isLoading.value = true;

    try {
      console.log('Email:', email.value, 'Phone:', phone_number.value, 'Password:', password.value);
      
      const user = await store.login({
        email : email.value,
        phone_number : phone_number.value,
        password : password.value
      })
      
      console.log('API Response:', user);
      
      router.push({ name: 'userUpdatePassword' });
    } catch (error) {
      if(error.response.status === 401){
        errors.value.password = "The provided credentials are incorrect. Please try again";
      }
      else
        router.push(`/user/error/${error.response ? error.response.status : ''}`);
    } finally {
      isLoading.value = false;
    }
  } else {
    console.log('Validation errors:', errors.value);
  }
};
</script>

<template>
  <registrationForm 
    :handle-submit="handleSubmit" 
    title="Register" 
    :is-loading="isLoading"
    secondary-message_Message="Aldready have an account?" 
    secondary-message_Action="login"
    top-bar-back-to = "/user"
  >
    <formInput 
      type="text"
      name="identifier"
      id="identifier"
      placeholder="Email or phone number"
      :model-value="identifier"
      @update:model-value="identifier = $event"
      :error-message="errors.identifier"
    />
    <input type="hidden" name="email" :value="email" />
    <input type="hidden" name="phone_number" :value="phone_number" />
    <formInput 
      type="password"
      name="password"
      id="password"
      placeholder="ID number"
      :model-value="password"
      @update:model-value="password = $event"
      :error-message="errors.password"
    />
  </registrationForm>
</template>