<template>
  <q-page padding>
    <q-form
      @submit="onSubmit"
      class="row q-col-gutter-sm"
    >
      <q-input
        outlined
        v-model="form.nome_completo"
        label="Nome Completo *"
        lazy-rules
        class="col-lg-12 col-xs-12"
        :rules="[ val => val && val.length > 0 || 'Por favor, digite o nome completo do funcionário']"
      />
      <q-input
        outlined
        v-model="form.cpf"
        label="CPF *"
        lazy-rules
        class="col-lg-6 col-xs-6"
        :rules="[ val => val && val.length > 3 || 'Por favor, digite o CPF do funcionário']"
      />
      <q-input filled v-model="form.data_nascimento" mask="##/##/####" label="Data de Nascimento" :rules="[val => validateDate(val) || 'Por favor, digite uma data válida']">
        <template v-slot:append>
          <q-icon name="event" class="cursor-pointer">
            <q-popup-proxy cover transition-show="scale" transition-hide="scale">
              <q-date v-model="form.data_nascimento" mask="DD/MM/YYYY">
                <div class="row items-center justify-end">
                  <q-btn v-close-popup label="Close" color="primary" flat />
                </div>
              </q-date>
            </q-popup-proxy>
          </q-icon>
        </template>
      </q-input>
      <q-toggle v-model="form.portador_comorbidade" label="Portador de Comorbidade" />
      <div class="col-12 q-gutter-sm">
        <q-btn
          type="submit"
          label="Salvar"
          color="primary"
          class="col-12"
        />
        <q-btn
          type="reset"
          label="Cancelar"
          color="white"
          text-color="primary"
          class="col-12"
          :to="{ name: 'listFuncionarios' }"
        />
      </div>
    </q-form>
  </q-page>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import funcionarioService from 'src/services/funcionarios'
import { useQuasar } from 'quasar'
import { formatDateToPtBr, formatDateToEnUS, validateDate } from 'boot/helpers'

export default defineComponent({
  name: 'FormFuncionario',
  setup () {
    const { post, getById, update } = funcionarioService()
    const $q = useQuasar()
    const router = useRouter()
    const route = useRoute()
    const form = ref({
      nome_completo: '',
      cpf: '',
      data_nascimento: '',
      portador_comorbidade: false
    })

    onMounted(async () => {
      if (route.params.id) {
        const response = await getById(route.params.id)
        form.value = {
          ...response,
          data_nascimento: formatDateToPtBr(response.data_nascimento)
        }
        form.value.portador_comorbidade = !!response.portador_comorbidade
      }
    })

    const onSubmit = async () => {
      try {
        if (form.value.id) {
          const response = await update({
            ...form.value, data_nascimento: formatDateToEnUS(form.value.data_nascimento)
          })
          console.log('response', response)
          $q.notify({
            icon: 'check',
            color: 'positive',
            position: 'top',
            message: 'Funcionário atualizado com sucesso!'
          })
          router.push({ name: 'listFuncionarios' })
        } else {
          await post({
            ...form.value, data_nascimento: formatDateToEnUS(form.value.data_nascimento)
          })
          $q.notify({
            icon: 'check',
            color: 'positive',
            position: 'top',
            message: 'Funcionário cadastrado com sucesso!'
          })
          router.push({ name: 'listFuncionarios' })
        }
      } catch (error) {
        $q.notify({
          icon: 'error',
          color: 'negative',
          position: 'top',
          message: error.message
        })
      }
    }

    return {
      form,
      onSubmit,
      formatDateToPtBr,
      validateDate
    }
  }
})
</script>
