<!-- resources/views/admin/products/create_form.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            フォーム作成
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-semibold">フォーム項目の編集</h3>

                    <div class="flex w-full justify-between gap-10">
                        <div class="flex-1 bg-gray-100 p-4 rounded">
                            <h4 class="mb-4">フォームプレビュー:</h4>
                            <form id="custom-form">
                                <div id="form-fields">
                                    <div class="form-item mb-4" draggable="true">
                                        <label for="name" class="block text-sm font-bold text-gray-700">名前</label>
                                        <input type="text" name="name" id="name" class="form-input mt-1 block w-full" />
                                        <button type="button" onclick="removeItem(this)"><svg
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                fill="currentColor" class="size-5">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="form-item mb-4" draggable="true">
                                        <label for="email" class="block text-sm font-bold text-gray-700">メールアドレス</label>
                                        <input type="email" name="email" id="email"
                                            class="form-input mt-1 block w-full" />
                                        <button type="button" onclick="removeItem(this)"><svg
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                fill="currentColor" class="size-5">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <button type="button" onclick="addField()">追加</button>
                                <button type="submit"
                                    class="px-4 py-2 bg-blue-500 text-white rounded">フォームコードの生成</button>
                            </form>
                        </div>

                        <div>
                            <h4>生成されたフォームコード:</h4>
                            <textarea id="generated-code" class="form-input mt-1 block w-full" readonly></textarea>
                        </div>
                    </div>




                </div>
            </div>
        </div>
    </div>

    <script>
        // フォーム項目を動的に追加
        function addField() {
            const formFieldsDiv = document.getElementById('form-fields');
            const newField = document.createElement('div');
            newField.classList.add('form-item', 'mb-4');
            newField.setAttribute('draggable', 'true');
            newField.innerHTML = `
                <label class="block text-sm font-bold text-gray-700">新しいフィールド</label>
                <input type="text" name="new_field" class="form-input mt-1 block w-full" />
                <button type="button" onclick="removeItem(this)">削除</button>
            `;
            formFieldsDiv.appendChild(newField);
        }

        // 削除ボタンをクリックでその項目を削除
        function removeItem(button) {
            button.parentElement.remove();
        }

        // ドラッグ&ドロップの設定
        const formFields = document.getElementById('form-fields');
        formFields.addEventListener('dragstart', (e) => {
            if (e.target && e.target.classList.contains('form-item')) {
                e.dataTransfer.setData('text/plain', e.target.innerHTML);
                e.target.style.opacity = '0.5';
            }
        });
        formFields.addEventListener('dragend', (e) => {
            if (e.target && e.target.classList.contains('form-item')) {
                e.target.style.opacity = '1';
            }
        });

        formFields.addEventListener('dragover', (e) => {
            e.preventDefault();
            if (e.target && e.target.classList.contains('form-item')) {
                e.target.style.border = '2px solid #ccc';
            }
        });

        formFields.addEventListener('dragleave', (e) => {
            if (e.target && e.target.classList.contains('form-item')) {
                e.target.style.border = 'none';
            }
        });

        formFields.addEventListener('drop', (e) => {
            e.preventDefault();
            if (e.target && e.target.classList.contains('form-item')) {
                const draggedHTML = e.dataTransfer.getData('text/plain');
                e.target.insertAdjacentHTML('beforebegin', draggedHTML);
                e.target.style.border = 'none';
            }
        });

        // フォームコードを生成
        document.getElementById('custom-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const formItems = document.querySelectorAll('.form-item');
            let formHTML = '<form action="/thank-you" method="GET">';

            formItems.forEach(item => {
                const input = item.querySelector('input');
                const label = item.querySelector('label').innerText;
                formHTML += `
                    <label for="${input.name}">${label}</label>
                    <input type="text" name="${input.name}" class="form-input mt-1 block w-full" />
                `;
            });

            formHTML += '<button type="submit">送信</button></form>';
            document.getElementById('generated-code').value = formHTML;
        });
    </script>
</x-app-layout>
