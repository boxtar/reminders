<template>
    <div class="w-full max-w-xs">
        <form :action="form.action" class="bg-white rounded px-8 pt-6 pb-8 mb-4" method="post">
            <!-- Csrf -->
            <input
                type="hidden"
                :name="csrf && csrf.name && csrf.name.key"
                :value="csrf && csrf.name && csrf.name.value"
            />
            <input
                type="hidden"
                :name="csrf && csrf.token && csrf.token.key"
                :value="csrf && csrf.token && csrf.token.value"
            />

            <!-- Email -->
            <div class="">
                <label class="mb-1 block text-gray-600 text-xs" for="email">
                    Email
                </label>
                <input
                    class="appearance-none w-full p-2 text-gray-700 border rounded leading-tight focus:outline-none focus:border-blue-400"
                    :class="{ 'border-red-500': errors && errors.email }"
                    id="email"
                    name="email"
                    type="text"
                    :value="old && old.email"
                />
                <p
                    v-show="errors && errors.email"
                    v-text="errors && errors.email && errors.email[0]"
                    class="mt-2 text-red-500 text-xs italic"
                ></p>
            </div>

            <!-- Password -->
            <div class="mt-6">
                <label class="mb-1 block text-gray-600 text-xs" for="password">
                    Password
                </label>
                <input
                    class="appearance-none w-full p-2 text-gray-700 border rounded leading-tight focus:outline-none focus:border-blue-400"
                    :class="{ 'border-red-500': errors && errors.password }"
                    id="password"
                    name="password"
                    type="password"
                />
                <p
                    v-show="errors && errors.password"
                    v-text="errors && errors.password && errors.password[0]"
                    class="mt-2 text-red-500 text-xs italic"
                ></p>
            </div>

            <div class="mt-8 flex items-center justify-between">
                <button
                    class="bg-blue-600 focus:outline-none focus:shadow-outline hover:bg-blue-700 px-4 py-2 rounded text-white w-full"
                    type="submit"
                >
                    Login
                </button>
            </div>
        </form>
        <p class="text-center text-gray-500 text-xs">Need an account? Register <a :href="form.registerLink">here</a>.</p>
    </div>
</template>

<script>
export default {
    props: ["errors", "old", "csrf"],
    data() {
        return {
            form: {
                action: undefined,
                registerLink: undefined
            },
        };
    },
    mounted() {
        this.form.action = window.App.routes["login.store"];
        this.form.registerLink = "/register";
    },
};
</script>

<style></style>
