/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./src/*.php", "./includes/*.php" ,"./admin/*.php", "./adminSQL/*.php", "./global/*.php" , "./teacher/*.php" , "./teacherSQL/*.php", "./student/*.php" , "./studentSQL/*.php"],
  theme: {
    extend: {
      colors :{
        "wheat": "#f5deb3",
        "myDark": "#031326"
      }
    },
  },
  plugins: [],
}