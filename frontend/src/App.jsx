import { BrowserRouter as Router, Routes, Route, Link } from "react-router-dom";
import { LoginForm } from "./components/ui/login-form";
import { RegisterForm } from "./components/ui/register-form";

function App() {
  return (
    <Router>
      <div className="flex flex-col items-center justify-center min-h-svh">
        <Routes>
          <Route path="/login" element={<LoginForm />} />
          <Route path="/register" element={<RegisterForm />} />
          <Route path="/" element={<LoginForm />} />
        </Routes>
      </div>
    </Router>
  );
}

export default App;
