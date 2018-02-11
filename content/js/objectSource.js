function changeSourceType(id, element) {
    if (element.value.length == 0) {
        document.getElementById("identifikator" + id).value = "";
        document.getElementById("identifikator" + id).disabled = true;
    } else {
        document.getElementById("identifikator" + id).disabled = false;
    }
}