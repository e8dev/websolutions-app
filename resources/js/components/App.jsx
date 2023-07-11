// resources/js/App.jsx
import React, {useState, useEffect} from 'react';
import { createRoot } from 'react-dom/client';
import axios from 'axios';

import 'bootstrap/dist/css/bootstrap.min.css';


export default function App(){

    const [monthlyData, setMonthlyData] = useState([]);

    const [annualInterestRate, setAnnualInterestRate] = useState(10);//%
    const [loanTerm, setLoanTerm] = useState(10);//years
    const [loanAmount, setLoanAmount] = useState(100000);
    const [extraPayment, setExtraPayment] = useState(0);
    const [saveToDb, setSaveToDb] = useState(true);

    const [showError, setShowError] = useState(false);
    const [errMsg, setErrMsg] = useState("");

    async function calculate(){

        let postData = {
            "annual_interest_rate": annualInterestRate,
            "loan_term": loanTerm,
            "loan_amount": loanAmount,
            "extra_payment": extraPayment,
            "save_to_db": saveToDb
        }

        const response = await axios.post("http://localhost/api/calculate", postData).catch(function(error){
            console.log("err",error.response.data.data);
        });

        console.log("test",response);

        if(response.data.success === true){
            setMonthlyData(response.data.data.monthly_data);
        }else{
            setShowError(true);
            setErrMsg(response.data.message);
        }

    }

    function formatNumber(number){

        return number.toLocaleString('en-US', {maximumFractionDigits:2});
    }

    return(
        <>
        <div className='container'>
            <div className='text-center'>
                <h1 className='title'>Mortgage calculator</h1>
            </div>
        <div className='row'>
            <div className='col-md-3'>
            <div className='card p-6 calc-form'>
            {errMsg}
                { showError && (
                    <div className='error'>{errMsg}</div>
                ) }
                <div className='mb-6'>
                    <label>Annual interest</label>
                    <input className='form-control' value={annualInterestRate} placeholder="annual interest" onChange={(e) => setAnnualInterestRate(e.target.value)} />
                </div>
                <div className='mb-6'>
                    <label>Loan term, years</label>
                    <input className='form-control' value={loanTerm} placeholder="loan term, years" onChange={(e) => setLoanTerm(e.target.value)} />
                </div>
                <div className='mb-6'>
                    <label>Loan amount</label>
                    <input className='form-control' value={loanAmount} placeholder="loan amount" onChange={(e) => setLoanAmount(e.target.value)} />
                </div>
                <div className='mb-6'>
                    <label>Extra monthly payment</label>
                    <input className='form-control' value={extraPayment} placeholder="extra amount" onChange={(e) => setExtraPayment(e.target.value)} />
                </div>
                <div className='mb-6'>
                    <input className='checkbox' type="checkbox" onChange={(e) => setSaveToDb(e.target.checked)} checked={saveToDb} /> Save to Database?
                </div>
                <div className='mb-6'>
                    <button className='btn btn-primary' onClick={() => {calculate()}}>Calculate</button>
                </div>
            </div>
            </div>
            <div className='col-md-9'>

                <div className='card my-6 schedule-table-wrapper'>
                    <table className="schedule-table">
                        <thead>
                        <tr>
                            <th>Month</th>
                            <th>Monthly payment</th>
                            <th>Loan start balance</th>
                            <th>Loan end balance</th>
                            <th>Principal</th>
                            <th>Interest</th>
                        </tr>
                        </thead>
                        <tbody>
                        { monthlyData.length > 0 && monthlyData.map(function(item, index){
                            return (
                                <tr className='' key={index}>
                                    <td>{item.month_number}</td>
                                    <td>{formatNumber(item.monthly_payment)}</td>
                                    <td>{formatNumber(item.starting_balance)}</td>
                                    <td>{formatNumber(item.ending_balance)}</td>
                                    <td>{formatNumber(item.principal)}</td>
                                    <td>{formatNumber(item.month_interest)}</td>
                                </tr>
                            );
                        })}
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
        
        </div>
        </>
            
    );
}

if(document.getElementById('root')){
    createRoot(document.getElementById('root')).render(<App />)
}