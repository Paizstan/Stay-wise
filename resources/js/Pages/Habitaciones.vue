const saveOrUpdate = async () => { submitted.value = true; if
(habitacion?.value.nombre?.trim()) { const formData = new FormData();
formData.append('nombre', habitacion.value.nombre || '');
formData.append('tipo', habitacion.value.tipo || '');
formData.append('capacidad', habitacion.value.capacidad || 1);
formData.append('descripcion', habitacion.value.descripcion || '');
formData.append('precio', habitacion.value.precio || 0); // Agregar las imágenes
al formData solo si hay nuevas imágenes if (imagenes.value.length) {
imagenes.value.forEach((img, index) => { formData.append(`imagenes[${index}]`,
img); }); } try { let response; if (habitacion.value.id) { // Actualizar la
habitación usando POST con _method=PUT formData.append('_method', 'PUT'); //
Añade este campo para indicar que es una actualización response = await
axios.post(`${url}/${habitacion.value.id}`, formData, { headers: {
'Content-Type': 'multipart/form-data' } }); if (response.status === 200) { const
{ habitacion: updatedHabitacion, message } = response.data; const index =
findIndexById(updatedHabitacion.id); if (index !== -1) {
habitaciones.value[index] = updatedHabitacion; } toast.add({ severity:
'success', summary: 'Actualizado!', detail: message, life: 3000 }); dialog.value
= false; habitacion.value = {}; } } else { // Agregar una nueva habitación
response = await axios.post(url, formData, { headers: { 'Content-Type':
'multipart/form-data' } }); if (response.status === 201) { const { habitacion:
nuevaHabitacion, message } = response.data;
habitaciones.value.unshift(nuevaHabitacion); toast.add({ severity: 'success',
summary: 'Registrado!', detail: message, life: 3000 }); dialog.value = false;
habitacion.value = {}; } } imagenes.value = []; imagesPreview.value = []; }
catch (err) { if (err.response && err.response.status === 409) { const { message
} = err.response.data; toast.add({ severity: 'warn', summary: 'Advertencia',
detail: message, life: 3000 }); } else if (err.response && err.response.status
=== 500) { const { error } = err.response.data; toast.add({ severity: 'error',
summary: 'Error!', detail: error, life: 3000 }); } else { toast.add({ severity:
'error', summary: 'Error', detail: `${err.response.data.message}, Error al
guardar el registro`, life: 3000 }); } } } };
