<?php 

namespace Yega\Xval;

class DFC {
    
    protected $mEBIT;
    protected $mTaxPaid;
    protected $mTaxAdjustedEBIT;
    protected $mNonCashCharges_Income;
    protected $mDepreciation;
    
    protected $mTotalCurrentAssets__Previous;
    protected $mTotalCurrentAssets__Current;

    protected $mTotalCurrentLiabilities__Previous;
    protected $mTotalCurrentLiabilities__Current;

    protected $mCapitalExpenditure;
    protected $mPerpetuityGrowthRate;
    
    protected $mPublicInput;
    protected $mClientInput;

    /**
     * @param array $publicInput
     * @param array $clientInput
     * 
     */
    public function __construct(array $publicInput, array $clientInput) 
    {
        $this->mPublicInput = $publicInput;
        $this->mClientInput = $clientInput;
    }    

    /**
     * Returns [low, high]
     * @return Array 
     *
     */
    public function doValuation() 
    {
        return [
            $this->doValuationLow, 
            $this->doValuationHigh
        ];
    }

    /**
     *
     * Calculate LOW value of the valuation range
     *
     */
    public function doValuationLow() 
    {
        return  ($this->doFreeCashFlows() * (1 + $this->mPublicInput['perpetuity_growth_rate_high'])) / 
                ($this->doWACC__high() - $this->doPerpetuityGrowthRate__high());

    }
    
    /**
        *
        * Calculate HIGH value of the valuation range
        *
        */
    public function doValuationHigh() 
    {
        return  ($this->doFreeCashFlows() * (1 + $this->mPublicInput['perpetuity_growth_rate_low'])) / 
                ($this->doWACC__low() - $this->doPerpetuityGrowthRate__low());

    }
    
    /**
     *
     * Total Current Assets + Total Current Liabilities
     *
     */
    protected function doChangeInWorkingCapital()
    {
        return ($this->doTotalCurrentAssets() + $this->doTotalCurrentLiabilities());
    }

    /**
     *
     * Total Current Assets (Current) - Total Current Assets (Previous)
     *
     */
    protected function doTotalCurrentAssets()
    {
        return ($this->mTotalCurrentAssets__Current - $this->mTotalCurrentAssets__Previous);
    }
    
    /**
     *
     * Total Current Liabilities (Current) - Total Current Liabilities (Previous)
     *
     */
    protected function doTotalCurrentLiabilities()
    {
        return ($this->mTotalCurrentLiabilities__Current - $this->mTotalCurrentLiabilities__Previous);
    }
    
    /**
     * Tax adjusted EBIT  + SUM(
     *                          Depreciation, 
     *                          Change in Working Capital, 
     *                          Total Current Assets, 
     *                          Total Current Liabilities
     *                      )
     *
     */
    protected function doFreeCashFlows()
    {
        $sum =  $this->mDepreciation + 
                $this->doChangeInWorkingCapital() +
                $this->doTotalCurrentAssets() + 
                $this->doTotalCurrentLiabilities() + 
                $this->mCapitalExpenditure;

        return $mTaxAdjustedEBIT + $sum;
    }

    protected function doTerminalValue() 
    {
        return  ($this->doFreeCashFlows() * (1 + $this->mPublicInput['perpetuity_growth_rate'])) / 
                ($this->doWACC() - $this->mPublicInput['perpetuity_growth_rate']);
    }
    
    /**
     *
     * Calculate WACC
     *
     */
    protected function doWACC() 
    {
        return  ($this->mPublicInput['debt_of_total_capital'] * $this->mPublicInput['after_tax_cost_of_debt']) + 
                ($this->mPublicInput['equity_to_total_capital'] * $this->mPublicInput['cost_of_equity']);
    }

    /**
     *
     * Calculate Perpetuity Growth Rate (low)
     *
     */
     protected function doPerpetuityGrowthRate__low() 
     {
         return ($this->mPublicInput['perpetuity_growth_rate'] - $this->mPublicInput['perpetuity_growth_rate_variation']);
     }
     
    /**
    *
    * Calculate Perpetuity Growth Rate (high)
    *
    */
    protected function doPerpetuityGrowthRate__high() 
    {
        return ($this->mPublicInput['perpetuity_growth_rate'] + $this->mPublicInput['perpetuity_growth_rate_variation']);
    }

    /**
     *
     * Calculate WACC (low)
     *
     */
    protected function doWACC__low() 
    {
        return ($this->doWACC() + $this->mPublicInput['WACC_variation']);
    }
    
    /**
     *
     * Calculate WACC (high)
     *
     */
    protected function doWACC__high() 
    {
        return ($this->doWACC() - $this->mPublicInput['WACC_variation']);
    }


    
}