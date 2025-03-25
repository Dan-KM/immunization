import { defineStore } from 'pinia';
import VaccinesService from '@/services/VaccinesService';

export const useVaccineStore = defineStore('vaccines', {
    state: () => ({
        allVaccines: [],
    }),
    actions: {
        async fetchAllVaccines() {
            try {
                const vaccineService = new VaccinesService();
                this.allVaccines = await vaccineService.getAllVaccines();
            } catch (error) {
                console.error('Error fetching all vaccines:', error);
                throw error;
            }
        },
        async fetchVaccineById(vaccine_id) {
            try {
                const vaccineService = new VaccinesService();
                return await vaccineService.getVaccineById(vaccine_id);
            } catch (error) {
                console.error(`Error fetching vaccine ${vaccine_id}:`, error);
                throw error;
            }
        }
    },
    getters: {
        getVaccineById: (state) => (vaccine_id) => {
            return state.allVaccines.find(vaccine => vaccine.id === vaccine_id);
        }
    }
});
