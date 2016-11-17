function validasian() {
			//validasi guru
			var tea_name = document.forms["sekolah"]["tea_name"].value;
			if(!tea_name.match(/^[a-zA-Z ]+$/)){
				alert("Nama hanya boleh Huruf \n");
		 		document.forms["sekolah"]["tea_name"].focus();
				return(false);
			};
			var tea_email = document.forms["sekolah"]["tea_email"].value;
			if(!tea_email.match(/^[0-9a-zA-Z.@_]+$/)){
				alert("Email harus tanpa spasi dan karakter tambahan hanya (.) dan (_) \n");
				document.forms["sekolah"]["tea_email"].focus();
				return(false);
			};
			var tea_phone = document.forms["sekolah"]["tea_phone"].value;
			if(!tea_phone.match(/^[0-9]+$/)){
				alert("Nomor telepon hanya boleh angka\n");
				document.forms["sekolah"]["tea_phone"].focus();
				return(false);
			};
			var tea_pass = document.forms["sekolah"]["tea_password"].value;
			if(tea_pass.length<=5){
				alert("Password harus lebih dari 5 karakter \n");
				document.forms["sekolah"]["tea_password"].focus();
				return(false);
			};
			//validasi siswa
			var stu_name = document.forms["sekolah"]["stu_name"].value;
			if(!stu_name.match(/^[a-zA-Z ]+$/)){
				alert("Nama hanya boleh Huruf \n");
		 		document.forms["sekolah"]["stu_name"].focus();
				return(false);
			};
			var stu_email = document.forms["sekolah"]["stu_email"].value;
			if(!stu_email.match(/^[0-9a-zA-Z.@_]+$/)){
				alert("Email harus tanpa spasi dan karakter tambahan hanya (.) dan (_) \n");
				document.forms["sekolah"]["stu_email"].focus();
				return(false);
			};
			var pass = document.forms["sekolah"]["password"].value;
			if(pass.length<=5){
				alert("Password harus lebih dari 5 karakter \n");
				document.forms["sekolah"]["password"].focus();
				return(false);
			};
			var stu_nisn = document.forms["sekolah"]["stu_nisn"].value;
			if(!stu_nisn.match(/^[0-9]+$/)){
				alert("NISN hanya boleh angka");
				document.forms["sekolah"]["stu_nisn"].focus();
				return(false);
			};
			if (stu_nisn.length!=10) {
				alert("NISN harus pas 10 digit angka ");
				document.forms["sekolah"]["stu_nisn"].focus();
				return(false);
			};
			//validasi orang tua
			var par_name = document.forms["sekolah"]["par_name"].value;
			if(!par_name.match(/^[a-zA-Z ]+$/)){
				alert("Nama hanya boleh Huruf \n");
		 		document.forms["sekolah"]["par_name"].focus();
				return(false);
			};
			var par_email = document.forms["sekolah"]["par_email"].value;
			if(!par_email.match(/^[0-9a-zA-Z.@_]+$/)){
				alert("Email harus tanpa spasi dan karakter tambahan hanya (.) dan (_) \n");
				document.forms["sekolah"]["par_email"].focus();
				return(false);
			};
			var par_phone = document.forms["sekolah"]["par_phone"].value;
			if(!par_phone.match(/^[0-9]+$/)){
				alert("Nomor telepon hanya boleh angka\n");
				document.forms["sekolah"]["par_phone"].focus();
				return(false);
			};
			var par_pass = document.forms["sekolah"]["par_password"].value;
			if(tea_pass.length<=5){
				alert("Password harus lebih dari 5 karakter \n");
				document.forms["sekolah"]["par_password"].focus();
				return(false);
			};
			var par_nisn = document.forms["sekolah"]["par_nisn"].value;
			if(!stu_nisn.match(/^[0-9]+$/)){
				alert("NISN hanya boleh angka");
				document.forms["sekolah"]["par_nisn"].focus();
				return(false);
			};
			if (par_nisn.length!=10) {
				alert("NISN harus pas 10 digit angka ");
				document.forms["sekolah"]["par_nisn"].focus();
				return(false);
			};
		}