$(document).ready(function() {
    var $provinceSelect = $('select[name="calc_shipping_provinces"]');
    var stc = '';
    c.forEach(function(province, index) {
        index += 1;
        stc += '<option value="' + index + '">' + province + '</option>';
    });
    $provinceSelect.html('<option value="">Tỉnh / Thành phố</option>' + stc);

    if (localStorage.getItem('address_1_saved')) {
        var savedProvince = localStorage.getItem('address_1_saved');
        $provinceSelect.find('option').each(function() {
            if ($(this).text() === savedProvince) {
                $(this).prop('selected', true);
                $('#calc_shipping_provinces_text').val(savedProvince);
            }
        });
    }

    $provinceSelect.on('change', function() {
        var selectedProvinceText = $(this).find('option:selected').text();
        $('#calc_shipping_provinces_text').val(selectedProvinceText);
        localStorage.setItem('address_1_saved', selectedProvinceText);

        var index = $(this).prop('selectedIndex') - 1;
        var districtOptions = '';
        if (index >= 0 && arr[index]) {
            arr[index].forEach(function(district) {
                districtOptions += '<option value="' + district + '">' + district + '</option>';
            });
        }
        $('select[name="calc_shipping_district"]').html('<option value="">Quận / Huyện</option>' + districtOptions);
    });

    if (localStorage.getItem('district')) {
        $('select[name="calc_shipping_district"]').html(localStorage.getItem('district'));
    }

    $('select[name="calc_shipping_district"]').on('change', function() {
        var selectedDistrict = $(this).find('option:selected').text();
        localStorage.setItem('address_2_saved', selectedDistrict);
        localStorage.setItem('district', $(this).html());
    });
});